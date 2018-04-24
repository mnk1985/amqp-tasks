<?php namespace App\Amqp\TasksBundle\Manager;

use App\Amqp\TasksBundle\DTO\SerializableDTOInterface;

interface TaskManagerInterface
{
    public function consume(string $queueName, array $options = []);
    public function publish(string $queueName, SerializableDTOInterface $data, array $options = []);
}