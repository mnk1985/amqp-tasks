<?php namespace AmqpTasksBundle\Tasks;

use AmqpTasksBundle\DTO\SerializableDTOInterface;

interface TaskInterface
{
    public function getQueueName(): string;
    public function getHandler(): TaskHandlerInterface;
    public function setHandler(TaskHandlerInterface $handler);
    public function getDTOSerializer(): SerializableDTOInterface;
    public function setDTOSerializer(SerializableDTOInterface $serializer);

}