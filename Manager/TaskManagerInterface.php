<?php namespace AmqpTasksBundle\Manager;

use AmqpTasksBundle\Registry\RegistryInterface;

interface TaskManagerInterface
{
    public function consume(string $queueName, array $options = []);
    public function publish(string $queueName, $data, array $options = []);
    public function getRegistry(): RegistryInterface;
}