<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory;

use AmqpTasksBundle\Drivers\RabbitMQ\Configs\PrefetchOptions;

class PrefetchOptionsFactory
{
    public static function create($options): PrefetchOptions
    {
        $prefetchOptions = new PrefetchOptions();

        if (!empty($options['size'])){
            $prefetchOptions->size = $options['size'];
        }

        if (!empty($options['count'])){
            $prefetchOptions->count = $options['count'];
        }

        if (!empty($options['global'])){
            $prefetchOptions->global = (bool)$options['global'];
        }

        return $prefetchOptions;
    }
}