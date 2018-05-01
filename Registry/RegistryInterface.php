<?php namespace AmqpTasksBundle\Registry;

use AmqpTasksBundle\Tasks\TaskInterface;

interface RegistryInterface
{
    public function registerTask(TaskInterface $task);
    public function getTask(string $queueName): TaskInterface;

}