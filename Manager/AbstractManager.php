<?php namespace AmqpTasksBundle\Manager;

use AmqpTasksBundle\Exception\NotFoundQueueException;
use AmqpTasksBundle\Registry\Registry;
use AmqpTasksBundle\Tasks\TaskHandlerInterface;
use AmqpTasksBundle\Tasks\TaskInterface;

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

    abstract protected function consumeConcrete(string $queueName, TaskInterface $task, $options);

    public function consume(string $queueName, array $options = [])
    {
        /**  @var TaskInterface $task */
        if (!$task = $this->taskRegistry->getTask($queueName)) {
            throw new NotFoundQueueException('not found task for queueName '.$queueName);
        }

        $taskHandler = $task->getHandler();

        if (isset($options['iterations']) && $options['iterations'] >= 0) {
            $taskHandler->getConfig()->setIterationsCount($options['iterations']);
            unset($options['iterations']);
        }

        if (isset($options['attempts']) && $options['attempts'] >= 0) {
            $taskHandler->getConfig()->setMaxAttemptsCount($options['attempts']);
            unset($options['attempts']);
        }

        if (isset($options['delay']) && $options['delay'] >= 0) {
            $taskHandler->getConfig()->setDelay($options['delay']);
            unset($options['delay']);
        }

        if (isset($options['verbose'])) {
            $taskHandler->getConfig()->setVerboseMode(true);
            unset($options['verbose']);
        }

        return $this->consumeConcrete($queueName, $task, $options);
    }
}