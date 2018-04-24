<?php namespace App\Amqp\TasksBundle\Tests\Functional\Tasks;

use App\Amqp\TasksBundle\Tasks\AbstractTask;

class TestTask extends AbstractTask
{

    public function getQueueName(): string
    {
        return 'test_queue';
    }
}