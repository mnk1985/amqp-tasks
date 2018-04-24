<?php namespace App\Amqp\TasksBundle\Tasks;


interface TaskHandlerInterface
{
    public function process($message): bool;
    public function setVerboseMode(bool $verboseMode): void;
    public function setIterationsCount(int $iterCount): void;
    public function shouldBeExecuted(): bool;
}