<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs;

class PrefetchOptions
{
    /**  @var int */
    public $size = null;

    public $count = 1;
    /**  @var bool */
    public $global = null;

}