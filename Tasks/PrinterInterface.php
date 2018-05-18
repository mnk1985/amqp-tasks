<?php namespace AmqpTasksBundle\Tasks;

interface PrinterInterface
{
    public function print(string $message): void;
}