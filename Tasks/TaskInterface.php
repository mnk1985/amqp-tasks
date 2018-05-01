<?php namespace AmqpTasksBundle\Tasks;

interface TaskInterface
{
    public function getQueueName(): string;
    public function getHandler(): TaskHandlerInterface;
}