<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs;

class QueueOptions
{
    public $passive = false;
    public $durable = true;
    public $exclusive = false;
    public $autoDelete = false;
    public $nowait = false;

}