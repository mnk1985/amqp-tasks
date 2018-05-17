<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs;

class PrefetchOptions
{
    /**  @var int */
    private $size = null;

    private $count = 1;
    /**  @var bool */
    private $global = null;

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function isGlobal(): ?bool
    {
        return $this->global;
    }

    public function setGlobal(?bool $global): self
    {
        $this->global = $global;

        return $this;
    }

}