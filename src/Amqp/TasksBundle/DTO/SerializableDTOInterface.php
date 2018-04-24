<?php namespace App\Amqp\TasksBundle\DTO;

interface SerializableDTOInterface
{
    public function convertToString(): string;
    public static function createFromString(string $data): SerializableDTOInterface;
}