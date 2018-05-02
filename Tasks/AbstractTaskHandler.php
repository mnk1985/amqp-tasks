<?php namespace AmqpTasksBundle\Tasks;

use AmqpTasksBundle\DTO\SerializableDTOInterface;
use AmqpTasksBundle\DTO\TestDTO;
use AmqpTasksBundle\Exception\InvalidDTOException;

abstract class AbstractTaskHandler implements TaskHandlerInterface
{
    protected $iterationsCount = 0;
    protected $shouldBeExecuted = true;
    protected $verboseMode = false;

    public function setVerboseMode(bool $verboseMode): void
    {
        $this->verboseMode = $verboseMode;
    }

    public function setIterationsCount(int $iterCount): void
    {
        $this->iterationsCount = $iterCount;
    }

    public function shouldBeExecuted(): bool
    {
        if ($this->iterationsCount < 0) return false;
        if (!$this->shouldBeExecuted) return false;
        if ($this->iterationsCount === 0) return true;

        $this->iterationsCount--;

        if ($this->iterationsCount === 0) {
            $this->shouldBeExecuted = false;
        }

        return true;
    }
}