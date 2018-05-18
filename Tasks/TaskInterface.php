<?php namespace AmqpTasksBundle\Tasks;

use AmqpTasksBundle\DTO\DTOSerializerInterface;

interface TaskInterface
{
    public function getQueueName(): string;
    public function getHandler(): TaskHandlerInterface;
    public function setHandler(TaskHandlerInterface $handler);
    public function getDTOSerializer(): DTOSerializerInterface;
    public function setDTOSerializer(DTOSerializerInterface $serializer);

}