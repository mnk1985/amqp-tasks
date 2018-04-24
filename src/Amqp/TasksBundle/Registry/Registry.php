<?php namespace App\Amqp\TasksBundle\Registry;

use App\Amqp\TasksBundle\Exception\DublicateQueueNameException;
use App\Amqp\TasksBundle\Exception\NotFoundTaskException;
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
            throw new NotFoundTaskException('queue with name '. $queueName. ' does not exist yet');
        }

        return $this->tasks->get($queueName);
    }
}