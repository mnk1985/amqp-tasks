<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory;

use AmqpTasksBundle\Drivers\RabbitMQ\Configs\QueueOptions;

class QueueOptionsFactory
{
    public static function create($options = []): QueueOptions
    {
        $queueOptions = new QueueOptions();

        if (!empty($options['exclusive'])){
            $queueOptions->setExclusive((bool)$options['exclusive']);
        }

        if (!empty($options['passive'])){
            $queueOptions->setPassive((bool)$options['passive']);
        }

        if (!empty($options['durable'])){
            $queueOptions->setDurable((bool)$options['durable']);
        }

        if (!empty($options['autoDelete'])){
            $queueOptions->setAutoDelete((bool)$options['autoDelete']);
        }

        if (!empty($options['nowait'])){
            $queueOptions->setNowait((bool)$options['nowait']);
        }

        return $queueOptions;
    }
}