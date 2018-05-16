<?php namespace AmqpTasksBundle\Drivers\RabbitMQ;

use AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory\ConsumeOptionsFactory;
use AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory\MessageOptionsFactory;
use AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory\PrefetchOptionsFactory;
use AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory\QueueOptionsFactory;
use AmqpTasksBundle\DTO\SerializableDTOInterface;
use AmqpTasksBundle\Manager\AbstractManager;
use AmqpTasksBundle\Registry\Registry;
use AmqpTasksBundle\Tasks\TaskHandlerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMQManager extends AbstractManager
{
    private const EXCHANGE = '';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection, Registry $taskRegistry)
    {
        parent::__construct($taskRegistry);
        $this->connection = $connection;
    }

    public function publish(string $queueName, SerializableDTOInterface $data, array $options = [])
    {
        $channel = $this->connection->channel();
        $task = $this->taskRegistry->getTask($queueName);

        $messageOptions = MessageOptionsFactory::create($options);
        $queueOptions = QueueOptionsFactory::create($options);

        $channel->queue_declare(
            $task->getQueueName(),
            $queueOptions->passive,
            $queueOptions->durable,
            $queueOptions->exclusive,
            $queueOptions->autoDelete
        );

        $msg = new AMQPMessage($data->convertToString(), [
            'delivery_mode' => $messageOptions->deliveryMode
        ]);

        $channel->basic_publish($msg, self::EXCHANGE, $task->getQueueName());
        $channel->close();
    }

    protected function consumeConcrete(string $queueName, TaskHandlerInterface $taskHandler, $options = [])
    {
        $channel = $this->getConsumeChannel($queueName, $taskHandler, $options);

        while (count($channel->callbacks) && $taskHandler->shouldBeExecuted()) {
            $channel->wait();
        }
        $channel->close();
    }

    private function getDeathCount(AMQPMessage $msg): ?int
    {
        $xdeath = null;
        $deathCount = null;
        $msgProps = $msg->get_properties();
        /** @var AMQPTable $applicationHeaders */
        $applicationHeaders = $msgProps['application_headers'] ?? null;
        if ($applicationHeaders) {
            $xdeath = $applicationHeaders->getNativeData()['x-death'] ?? null;
        }
        if ($xdeath) {
            $deathCount = $xdeath[0]['count'] ?? null;
        }
        return $deathCount;
    }

    private function republishWithDelay(string $queueName, AMQPMessage $msg, array $options = [])
    {
        $channel = $this->connection->channel();

        $queueOptions = QueueOptionsFactory::create($options);

        $exchangeRightNow = $queueName;
        $queueNameDelayed = $exchangeDelayed = $queueName.'.delayed';

        $channel->queue_declare(
            $queueName,
            $queueOptions->passive,
            $queueOptions->durable,
            $queueOptions->exclusive,
            $queueOptions->autoDelete
        );
        $channel->exchange_declare($exchangeRightNow,'direct');
        $channel->queue_bind($queueName, $exchangeRightNow);

        $taskHandler = $this->taskRegistry->getTask($queueName)->getHandler();

        $channel->queue_declare(
            $queueNameDelayed,
            $queueOptions->passive,
            $queueOptions->durable,
            $queueOptions->exclusive,
            $queueOptions->autoDelete,
            $queueOptions->nowait,
            array(
                'x-message-ttl' => array('I', $taskHandler->getDelay() * 1000), //convert to miliseconds
                "x-expires" => array("I", $taskHandler->getDelay() * 1000 + 1000),
                'x-dead-letter-exchange' => array('S', $exchangeRightNow)
            )
        );
        $channel->exchange_declare($exchangeDelayed, 'direct');
        $channel->queue_bind($queueNameDelayed, $exchangeDelayed);

        $channel->basic_publish($msg, $exchangeDelayed);
        $channel->close();
    }

    private function getConsumeChannel(string $queueName, TaskHandlerInterface $taskHandler, $options)
    {
        $channel = $this->connection->channel();

        $consumeOptions = ConsumeOptionsFactory::create($options);
        $queueOptions = QueueOptionsFactory::create($options);
        $prefetchOptions = PrefetchOptionsFactory::create($options);

        $channel->queue_declare(
            $queueName,
            $queueOptions->passive,
            $queueOptions->durable,
            $queueOptions->exclusive,
            $queueOptions->autoDelete
        );

        $channel->basic_qos(
            $prefetchOptions->size,
            $prefetchOptions->count,
            $prefetchOptions->global
        );

        $callback = function ($msg) use ($taskHandler, $queueName) {
            if ($taskHandler->process($msg->body)) {
                $taskHandler->printOutput('processed: '. $msg->body);
            } elseif ($taskHandler->shouldRetry($this->getDeathCount($msg))) {
                $taskHandler->printOutput('republishing: '. $msg->body);
                $this->republishWithDelay($queueName, $msg);
            } else {
                $taskHandler->printOutput('dropping: '. $msg->body);
            }
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_consume(
            $queueName,
            $consumeOptions->consumerTag,
            $consumeOptions->noLocal,
            $consumeOptions->noAck,
            $consumeOptions->exclusive,
            $consumeOptions->noWait,
            $callback
        );
        return $channel;
    }

}