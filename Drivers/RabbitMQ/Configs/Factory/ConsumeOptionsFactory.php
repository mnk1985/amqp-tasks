<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory;

use AmqpTasksBundle\Drivers\RabbitMQ\Configs\ConsumeOptions;

class ConsumeOptionsFactory
{
    public static function create($options): ConsumeOptions
    {
        $consumeOptions = new ConsumeOptions();

        if (!empty($options['consumerTag'])){
            $consumeOptions->consumerTag = $options['consumerTag'];
        }

        if (!empty($options['noLocal'])){
            $consumeOptions->noLocal = (bool)$options['noLocal'];
        }

        if (!empty($options['noAck'])){
            $consumeOptions->noAck = (bool)$options['noAck'];
        }

        if (!empty($options['noWait'])){
            $consumeOptions->noWait = (bool)$options['noWait'];
        }

        if (!empty($options['exclusive'])){
            $consumeOptions->exclusive = (bool)$options['exclusive'];
        }

        return $consumeOptions;
    }
}