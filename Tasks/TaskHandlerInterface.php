<?php namespace AmqpTasksBundle\Tasks;


interface TaskHandlerInterface
{
    public function process(string $message): bool;
    public function setVerboseMode(bool $verboseMode): void;
    public function setIterationsCount(int $iterCount): void;
    public function shouldBeExecuted(): bool;
}