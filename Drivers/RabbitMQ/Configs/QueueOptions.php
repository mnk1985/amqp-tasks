<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs;

class QueueOptions
{
    private $passive = false;
    private $durable = true;
    private $exclusive = false;
    private $autoDelete = false;
    private $nowait = false;

    public function isNowait(): ?bool
    {
        return $this->nowait;
    }

    public function setNowait(?bool $nowait): self
    {
        $this->nowait = $nowait;
        return $this;
    }

    public function isPassive(): ?bool
    {
        return $this->passive;
    }

    public function setPassive(?bool $passive): self
    {
        $this->passive = $passive;

        return $this;
    }

    public function isDurable(): ?bool
    {
        return $this->durable;
    }

    public function setDurable(?bool $durable): self
    {
        $this->durable = $durable;

        return $this;
    }

    public function isExclusive(): ?bool
    {
        return $this->exclusive;
    }

    public function setExclusive(?bool $exclusive): self
    {
        $this->exclusive = $exclusive;

        return $this;
    }

    public function isAutoDelete(): ?bool
    {
        return $this->autoDelete;
    }

    public function setAutoDelete(?bool $autoDelete): self
    {
        $this->autoDelete = $autoDelete;

        return $this;
    }

}