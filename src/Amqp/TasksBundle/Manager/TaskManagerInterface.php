<?php namespace App\Amqp\TasksBundle\Manager;

use App\Amqp\TasksBundle\DTO\SerializableDTOInterface;
use App\Amqp\TasksBundle\Tasks\TaskInterface;

interface TaskManagerInterface
{
    public function consume(TaskInterface $task, array $options = []);
    public function publish(string $queueName, SerializableDTOInterface $data, array $options = []);
}