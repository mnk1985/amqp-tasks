<?php namespace AmqpTasksBundle\Tasks;

interface TaskHandlerConfigInterface
{
    public function setVerboseMode(bool $verboseMode): void;
    public function isVerboseMode(): bool;

    public function setIterationsCount(int $iterationsCount): void;
    public function getIterationsCount(): int;

    public function setMaxAttemptsCount(int $attemptsCount);
    public function getMaxAttemptsCount(): int;

    public function setDelay(int $delay): void;
    public function getDelay(): int;
}