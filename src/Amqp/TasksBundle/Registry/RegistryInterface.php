<?php namespace App\Amqp\TasksBundle\Registry;

use App\Amqp\TasksBundle\Tasks\TaskInterface;

interface RegistryInterface
{
    public function registerTask(TaskInterface $task);
    public function getTask(string $queueName): TaskInterface;

}