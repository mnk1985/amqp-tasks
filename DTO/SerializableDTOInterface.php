<?php namespace AmqpTasksBundle\DTO;

interface SerializableDTOInterface
{
    public function convertToString(): string;
    public function createFromString(string $data): SerializableDTOInterface;
}