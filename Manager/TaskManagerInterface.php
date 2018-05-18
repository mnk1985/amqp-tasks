<?php namespace AmqpTasksBundle\Manager;

use AmqpTasksBundle\DTO\SerializableDTOInterface;

interface TaskManagerInterface
{
    public function consume(string $queueName, array $options = []);
    public function publish(string $queueName, $data, array $options = []);
}