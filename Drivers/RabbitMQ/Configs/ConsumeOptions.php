<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs;

class ConsumeOptions
{
    private $consumerTag = '';
    private $noLocal = false;
    private $noAck = false;
    private $exclusive = false;
    private $noWait = false;

    public function getConsumerTag(): ?string
    {
        return $this->consumerTag;
    }

    public function setConsumerTag(?string $consumerTag): self
    {
        $this->consumerTag = $consumerTag;

        return $this;
    }

    public function isNoLocal(): ?bool
    {
        return $this->noLocal;
    }

    public function setNoLocal(?bool $noLocal): self
    {
        $this->noLocal = $noLocal;

        return $this;
    }

    public function isNoAck(): ?bool
    {
        return $this->noAck;
    }

    public function setNoAck(?bool $noAck): self
    {
        $this->noAck = $noAck;

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

    public function isNoWait(): ?bool
    {
        return $this->noWait;
    }

    public function setNoWait(?bool $nowait): self
    {
        $this->noWait = $nowait;

        return $this;
    }

}