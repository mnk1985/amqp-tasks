<?php namespace App\Amqp\TasksBundle\Tasks;

interface TaskInterface
{
    public function getQueueName(): string;
    public function getHandler(): TaskHandlerInterface;
}