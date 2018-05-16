<?php namespace AmqpTasksBundle\Tasks;

class TaskHandlerConfig implements TaskHandlerConfigInterface
{
    protected $iterationsCount = 0;
    protected $verboseMode = false;
    protected $delay = 1; // in sec
    protected $maxAttemptsCount = 2;

    public function getIterationsCount(): int
    {
        return $this->iterationsCount;
    }

    public function setIterationsCount(?int $iterationsCount): void
    {
        $this->iterationsCount = $iterationsCount;
    }

    public function isVerboseMode(): bool
    {
        return $this->verboseMode;
    }

    public function setVerboseMode(?bool $verboseMode): void
    {
        $this->verboseMode = $verboseMode;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function setDelay(?int $delay): void
    {
        $this->delay = $delay;
    }

    public function getMaxAttemptsCount(): int
    {
        return $this->maxAttemptsCount;
    }

    public function setMaxAttemptsCount(?int $maxAttemptsCount): void
    {
        $this->maxAttemptsCount = $maxAttemptsCount;
    }
}