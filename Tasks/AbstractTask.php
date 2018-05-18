<?php namespace AmqpTasksBundle\Tasks;

use AmqpTasksBundle\DTO\SerializableDTOInterface;
use AmqpTasksBundle\Exception\NotFoundTaskHandlerException;

abstract class AbstractTask implements TaskInterface
{
    protected CONST HANDLER_SUFFIX = 'Handler';

    /**
     * @var TaskHandlerInterface
     */
    private $handler;

    /**
     * @var SerializableDTOInterface
     */
    private $dtoSerializer;

    public function getHandler(): TaskHandlerInterface
    {
        if (!$this->handler) {
            $className = static::class;
            $handlerClassName = $className.static::HANDLER_SUFFIX;

            if (!class_exists($handlerClassName)) {
                throw new NotFoundTaskHandlerException();
            }

            $this->handler = new $handlerClassName;
        }

        return $this->handler;

    }

    public function setHandler(TaskHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function setDTOSerializer(SerializableDTOInterface $serializer)
    {
        $this->dtoSerializer = $serializer;
    }
}