<?php namespace AmqpTasksBundle\Tasks;

use AmqpTasksBundle\DTO\DTOSerializerInterface;
use AmqpTasksBundle\DTO\TestDTO;
use AmqpTasksBundle\Exception\InvalidDTOException;

abstract class AbstractTaskHandler implements TaskHandlerInterface
{
    protected $shouldBeExecuted = true;

    /**
     * @var TaskHandlerConfigInterface
     */
    private $config;

    /**
     * @var PrinterInterface
     */
    private $printer;

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
            $this->getPrinter()->print($message);
        }
    }

    public function getConfig(): TaskHandlerConfigInterface
    {
        if (!$this->config) {
            $this->config = new TaskHandlerConfig();
        }

        return $this->config;
    }

    public function setConfig(TaskHandlerConfigInterface $config): void
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

    public function getPrinter(): PrinterInterface
    {
        if (!$this->printer) {
            $this->printer = new Printer();
        }

        return $this->printer;
    }

    public function setPrinter(PrinterInterface $printer): void
    {
        $this->printer = $printer;
    }

}