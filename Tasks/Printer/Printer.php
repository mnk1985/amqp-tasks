<?php namespace AmqpTasksBundle\Tasks\Printer;

use AmqpTasksBundle\Tasks\Printer\PrinterInterface;

class Printer implements PrinterInterface
{
    public function print(string $message): void
    {
        echo $message . PHP_EOL;
    }
}