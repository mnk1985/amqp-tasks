<?php namespace AmqpTasksBundle\Tasks;


interface TaskHandlerInterface
{
    public function process(string $message): bool;

    public function printOutput(string $message): void;
    public function shouldBeExecuted(): bool;
    public function shouldRetry(?int $deathCount): bool;
    public function getDelay(): int;

}