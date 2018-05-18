<?php namespace AmqpTasksBundle\DTO;

interface DTOSerializerInterface
{
    public function convertToString($dto): string;
    public function createDTO(string $data);
}