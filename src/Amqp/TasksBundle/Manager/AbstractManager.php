<?php namespace App\Amqp\TasksBundle\Manager;

use App\Amqp\TasksBundle\Exception\NotFoundQueueException;
use App\Amqp\TasksBundle\Registry\Registry;
use App\Amqp\TasksBundle\Tasks\TaskHandlerInterface;
use App\Amqp\TasksBundle\Tasks\TaskInterface;

abstract class AbstractManager implements TaskManagerInterface
{
    /**
     * @var Registry
     */
    protected $taskRegistry;

    public function __construct(Registry $taskRegistry)
    {
        $this->taskRegistry = $taskRegistry;
    }

    abstract protected function consumeConcrete(string $queueName, TaskHandlerInterface $handler, $options);

    public function consume(string $queueName, array $options = [])
    {
        /**  @var TaskInterface $task */
        if (!$task = $this->taskRegistry->getTask($queueName)) {
            throw new NotFoundQueueException('not found task for queueName '.$queueName);
        }

        $taskHandler = $task->getHandler();

        if (isset($options['iterations']) && $options['iterations'] >= 0) {
            $taskHandler->setIterationsCount($options['iterations']);
            unset($options['iterations']);
        }

        if (isset($options['verbose'])) {
            $taskHandler->setVerboseMode(true);
            unset($options['verbose']);
        }

        return $this->consumeConcrete($queueName, $taskHandler, $options);
    }
}