<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory;

use AmqpTasksBundle\Drivers\RabbitMQ\Configs\QueueOptions;

class QueueOptionsFactory
{
    public static function create($options = []): QueueOptions
    {
        $queueOptions = new QueueOptions();

        if (!empty($options['exclusive'])){
            $queueOptions->exclusive = (bool)$options['exclusive'];
        }

        if (!empty($options['passive'])){
            $queueOptions->passive = (bool)$options['passive'];
        }

        if (!empty($options['durable'])){
            $queueOptions->durable = (bool)$options['durable'];
        }

        if (!empty($options['autoDelete'])){
            $queueOptions->autoDelete = (bool)$options['autoDelete'];
        }

        if (!empty($options['nowait'])){
            $queueOptions->nowait = (bool)$options['nowait'];
        }

        return $queueOptions;
    }
}