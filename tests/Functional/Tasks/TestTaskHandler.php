<?php namespace App\Amqp\TasksBundle\Tests\Functional\Tasks;

use App\Amqp\TasksBundle\Tasks\AbstractTaskHandler;
use App\Amqp\TasksBundle\Tests\Functional\DTO\TestDTO;

class TestTaskHandler extends AbstractTaskHandler
{
    /**
     * @param TestDTO $message
     * @return bool
     */
    public function processConcrete($message): bool
    {
        if ($message->getFieldA() && $message->getFieldB()) {
            return true;
        }

        return false;
    }
}