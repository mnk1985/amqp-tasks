<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs;

use PhpAmqpLib\Message\AMQPMessage;

class MessageOptions
{
    public $deliveryMode = AMQPMessage::DELIVERY_MODE_PERSISTENT;

}