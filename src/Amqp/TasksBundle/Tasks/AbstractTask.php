<?php namespace App\Amqp\TasksBundle\Tasks;

use App\Amqp\TasksBundle\Exception\NotFoundTaskHandlerException;

abstract class AbstractTask implements TaskInterface
{
    protected CONST HANDLER_SUFFIX = 'Handler';

    public function getHandler(): TaskHandlerInterface
    {
        $className = static::class;
        $handlerClassName = $className.static::HANDLER_SUFFIX;

        if (!class_exists($handlerClassName)) {
            throw new NotFoundTaskHandlerException();
        }

        return new $handlerClassName;
    }
}