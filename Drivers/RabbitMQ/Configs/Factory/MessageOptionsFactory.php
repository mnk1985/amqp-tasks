<?php namespace AmqpTasksBundle\Drivers\RabbitMQ\Configs\Factory;

use AmqpTasksBundle\Drivers\RabbitMQ\Configs\MessageOptions;

class MessageOptionsFactory
{
    public static function create($options): MessageOptions
    {
        $messageOptions = new MessageOptions();

        if (!empty($options['delivery_mode'])){
            $messageOptions->setDeliveryMode($options['delivery_mode']);
        }

        return $messageOptions;
    }
}