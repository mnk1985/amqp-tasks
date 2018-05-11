<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory;

use AmqpTasksBundle\Drivers\RabbitMQ\Configs\ConsumeOptions;

class ConsumeOptionsFactory
{
    public static function create($options): ConsumeOptions
    {
        $consumeOptions = new ConsumeOptions();

        if (!empty($options['consumerTag'])){
            $consumeOptions->setConsumerTag($options['consumerTag']);
        }

        if (!empty($options['noLocal'])){
            $consumeOptions->setNoLocal((bool)$options['noLocal']);
        }

        if (!empty($options['noAck'])){
            $consumeOptions->setNoAck((bool)$options['noAck']);
        }

        if (!empty($options['noWait'])){
            $consumeOptions->setNoWait((bool)$options['noWait']);
        }

        if (!empty($options['exclusive'])){
            $consumeOptions->setExclusive((bool)$options['exclusive']);
        }

        return $consumeOptions;
    }
}