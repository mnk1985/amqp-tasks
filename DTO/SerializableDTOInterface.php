<?php namespace AmqpTasksBundle\DTO;

interface SerializableDTOInterface
{
    public function convertToString($dto): string;
    public function createDTO(string $data);
}