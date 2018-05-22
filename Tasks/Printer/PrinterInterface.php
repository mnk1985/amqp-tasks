<?php namespace AmqpTasksBundle\Tasks\Printer;

interface PrinterInterface
{
    public function print(string $message): void;
}