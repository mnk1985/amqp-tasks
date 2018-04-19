<?php namespace App\Amqp\TasksBundle\DTO;

interface SerializableDTOInterface
{
    public function convertToString(): string;
    public function createFromString(string $data): self;
}