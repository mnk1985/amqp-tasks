<?php namespace AmqpTasksBundle\Tasks;

class Printer implements PrinterInterface
{


    public function print(string $message): void
    {
        echo $message . PHP_EOL;
    }
}