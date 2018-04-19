<?php namespace App\Amqp\TasksBundle\Tasks;


interface TaskHandlerInterface
{
    public function process($message): bool;
    public function setVerboseMode(bool $verboseMode): void;
}