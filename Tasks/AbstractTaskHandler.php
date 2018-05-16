<?php namespace AmqpTasksBundle\Tasks;

use AmqpTasksBundle\DTO\SerializableDTOInterface;
use AmqpTasksBundle\DTO\TestDTO;
use AmqpTasksBundle\Exception\InvalidDTOException;

abstract class AbstractTaskHandler implements TaskHandlerInterface
{
    protected $shouldBeExecuted = true;

    /**
     * @var TaskHandlerConfigInterface
     */
    protected $config;

    public function shouldBeExecuted(): bool
    {
        if ($this->getConfig()->getIterationsCount() < 0) return false;
        if (!$this->shouldBeExecuted) return false;
        if ($this->getConfig()->getIterationsCount() === 0) return true;

        $this->getConfig()->setIterationsCount($this->getConfig()->getIterationsCount()-1);

        if ($this->getConfig()->getIterationsCount() === 0) {
            $this->shouldBeExecuted = false;
        }

        return true;
    }

    public function printOutput(string $message): void
    {
        if($this->getConfig()->isVerboseMode()) {
            echo $message.PHP_EOL;
        }
    }

    public function getConfig(): TaskHandlerConfigInterface
    {
        // TODO: get service from container
        if (!$this->config) {
            $this->config = new TaskHandlerConfig();
        }

        return $this->config;
    }

    public function setConfig(TaskHandlerConfigInterface $config)
    {
        $this->config = $config;
    }

    public function shouldRetry(?int $deathCount): bool
    {
        return $deathCount < $this->getConfig()->getMaxAttemptsCount();
    }

    public function getDelay(): int
    {
        return $this->getConfig()->getDelay();
    }

}