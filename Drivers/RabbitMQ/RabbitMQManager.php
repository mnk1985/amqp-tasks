<?php namespace AmqpTasksBundle\Drivers\RabbitMQ;

use AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory\ConsumeOptionsFactory;
use AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory\MessageOptionsFactory;
use AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory\PrefetchOptionsFactory;
use AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory\QueueOptionsFactory;
use AmqpTasksBundle\Manager\AbstractManager;
use AmqpTasksBundle\Registry\Registry;
use AmqpTasksBundle\Tasks\TaskInterface;
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

    public function publish(string $queueName, $dto, array $options = [])
    {
        $channel = $this->connection->channel();
        $task = $this->getRegistry()->getTask($queueName);

        $messageOptions = MessageOptionsFactory::create($options);
        $queueOptions = QueueOptionsFactory::create($options);

        $channel->queue_declare(
            $task->getQueueName(),
            $queueOptions->isPassive(),
            $queueOptions->isDurable(),
            $queueOptions->isExclusive(),
            $queueOptions->isAutoDelete()
        );

        $msg = new AMQPMessage($task->getDTOSerializer()->convertToString($dto), [
            'delivery_mode' => $messageOptions->getDeliveryMode()
        ]);

        $channel->basic_publish($msg, self::EXCHANGE, $task->getQueueName());
        $channel->close();
    }

    protected function consumeConcrete(string $queueName, TaskInterface $task, $options = [])
    {
        $channel = $this->getConsumeChannelWithCallback($queueName, $task, $options);

        while (count($channel->callbacks) && $task->getHandler()->shouldBeExecuted()) {
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
            $queueOptions->isPassive(),
            $queueOptions->isDurable(),
            $queueOptions->isExclusive(),
            $queueOptions->isAutoDelete()
        );
        $channel->exchange_declare($exchangeRightNow,'direct');
        $channel->queue_bind($queueName, $exchangeRightNow);

        $taskHandler = $this->getRegistry()->getTask($queueName)->getHandler();

        $channel->queue_declare(
            $queueNameDelayed,
            $queueOptions->isPassive(),
            $queueOptions->isDurable(),
            $queueOptions->isExclusive(),
            $queueOptions->isAutoDelete(),
            $queueOptions->isNowait(),
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

    private function getConsumeChannelWithCallback(string $queueName, TaskInterface $task, $options)
    {
        $channel = $this->connection->channel();

        $consumeOptions = ConsumeOptionsFactory::create($options);
        $queueOptions = QueueOptionsFactory::create($options);
        $prefetchOptions = PrefetchOptionsFactory::create($options);

        $channel->queue_declare(
            $queueName,
            $queueOptions->isPassive(),
            $queueOptions->isDurable(),
            $queueOptions->isExclusive(),
            $queueOptions->isAutoDelete()
        );

        $channel->basic_qos(
            $prefetchOptions->getSize(),
            $prefetchOptions->getCount(),
            $prefetchOptions->isGlobal()
        );

        $callback = function ($msg) use ($task, $queueName) {
            $taskHandler = $task->getHandler();
            if ($taskHandler->process($task->getDTOSerializer()->createDTO($msg->body))) {
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
            $consumeOptions->getConsumerTag(),
            $consumeOptions->isNoLocal(),
            $consumeOptions->isNoAck(),
            $consumeOptions->isExclusive(),
            $consumeOptions->isNoWait(),
            $callback
        );
        return $channel;
    }

}