<?php namespace App\Amqp\TasksBundle\Services;

use App\Amqp\TasksBundle\Exception\DublicateQueueNameException;
use App\Amqp\TasksBundle\Registry\RegistryInterface;
use App\Amqp\TasksBundle\Tasks\TaskInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Registry implements RegistryInterface
{
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function registerTask(TaskInterface $task)
    {
        if ($this->tasks->containsKey($task->getQueueName())) {
            throw new DublicateQueueNameException('queue with name '. $task->getQueueName(). ' already exists');
        }

        $this->tasks->set($task->getQueueName(), $task);
    }

    public function getTask(string $queueName): TaskInterface
    {
        if (!$this->tasks->containsKey($queueName)) {
            throw new \InvalidArgumentException('queue with name '. $queueName. ' does not exist yet');
        }

        return $this->tasks->get($queueName);
    }
}