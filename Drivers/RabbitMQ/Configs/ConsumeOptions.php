<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs;

class ConsumeOptions
{
    public $consumerTag = '';
    public $noLocal = false;
    public $noAck = false;
    public $exclusive = false;
    public $noWait = false;

}