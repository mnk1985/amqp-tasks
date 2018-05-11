<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory;

use AmqpTasksBundle\Drivers\RabbitMQ\Configs\PrefetchOptions;

class PrefetchOptionsFactory
{
    public static function create($options): PrefetchOptions
    {
        $prefetchOptions = new PrefetchOptions();

        if (!empty($options['size'])){
            $prefetchOptions->setSize($options['size']);
        }

        if (!empty($options['count'])){
            $prefetchOptions->setCount($options['count']);
        }

        if (!empty($options['global'])){
            $prefetchOptions->setGlobal((bool)$options['global']);
        }

        return $prefetchOptions;
    }
}