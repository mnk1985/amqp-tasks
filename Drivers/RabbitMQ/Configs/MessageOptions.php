<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs;

use PhpAmqpLib\Message\AMQPMessage;

class MessageOptions
{
    private $delivery_mode = AMQPMessage::DELIVERY_MODE_PERSISTENT;

    public function getDeliveryMode(): ?int
    {
        return $this->delivery_mode;
    }

    public function setDeliveryMode(?int $delivery_mode): self
    {
        $this->delivery_mode = $delivery_mode;

        return $this;
    }

}