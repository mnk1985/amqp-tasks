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
            $queueOptions->isPassive(),
            $queueOptions->isDurable(),
            $queueOptions->isExclusive(),
            $queueOptions->isAutoDelete()
        );

        $msg = new AMQPMessage($data->convertToString(), [
            'delivery_mode' => $messageOptions->getDeliveryMode()
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

    private function getConsumeChannel(string $queueName, TaskHandlerInterface $taskHandler, $options)
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

        $callback = function ($msg) use ($taskHandler) {
            $taskHandler->process($msg->body);
            $taskHandler->printOutput($msg->body);
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