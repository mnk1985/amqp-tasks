<?php namespace AmqpTasksBundle\Drivers\RabbitMQ;

use AmqpTasksBundle\DTO\SerializableDTOInterface;
use AmqpTasksBundle\Manager\AbstractManager;
use AmqpTasksBundle\Registry\Registry;
use AmqpTasksBundle\Tasks\TaskHandlerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQManager extends AbstractManager
{
    private const EXCHANGE = '';
    protected $iterationsCount = 0;
    protected $shouldBeExecuted = true;

    private $defaultConsumeOptions = [
        'consumerTag' => '',
        'noLocal' => false,
        'noAck' => false,
        'exclusive' => false,
        'nowait' => false,
    ];
    private $defaultMessageOptions = [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ];
    private $defaultQueueOptions = [
        'passive' => false,
        'durable' => true,
        'exclusive' => false,
        'autoDelete' => false,
    ];
    private $defaultPrefetchOptions = [
        'size' => null,
        'count' => 1,
        'global' => null
    ];

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

        $messageOptions = array_merge($this->defaultMessageOptions, $options);
        $queueOptions = array_merge($this->defaultQueueOptions, $options);

        $channel->queue_declare(
            $task->getQueueName(),
            $queueOptions['passive'],
            $queueOptions['durable'],
            $queueOptions['exclusive'],
            $queueOptions['autoDelete']
        );

        $msg = new AMQPMessage($data->convertToString(), $messageOptions);

        $channel->basic_publish($msg, self::EXCHANGE, $task->getQueueName());
        $channel->close();
    }

    protected function consumeConcrete(string $queueName, TaskHandlerInterface $taskHandler, $options = [])
    {
        $channel = $this->connection->channel();

        $callback = function ($msg) use ($taskHandler) {
            $taskHandler->process($msg->body);
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        $consumeOptions = array_merge($this->defaultConsumeOptions, $options);
        $queueOptions = array_merge($this->defaultQueueOptions, $options);

        $channel->queue_declare(
            $queueName,
            $queueOptions['passive'],
            $queueOptions['durable'],
            $queueOptions['exclusive'],
            $queueOptions['autoDelete']
        );

        $channel->basic_qos(
            $this->defaultPrefetchOptions['size'],
            $this->defaultPrefetchOptions['count'],
            $this->defaultPrefetchOptions['global']
        );
        $channel->basic_consume(
            $queueName,
            $consumeOptions['consumerTag'],
            $consumeOptions['noLocal'],
            $consumeOptions['noAck'],
            $consumeOptions['exclusive'],
            $consumeOptions['nowait'],
            $callback
        );
        while (count($channel->callbacks) && $taskHandler->shouldBeExecuted()) {
            $channel->wait();
        }
        $channel->close();
    }

}