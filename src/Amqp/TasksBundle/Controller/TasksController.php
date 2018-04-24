<?php namespace App\Amqp\TasksBundle\Controller;

use App\Amqp\TasksBundle\DTO\TestDTO;
use App\Amqp\TasksBundle\Manager\TaskManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TasksController extends Controller
{
    /**
     * @var TaskManagerInterface
     */
    private $taskManager;

    public function __construct(TaskManagerInterface $taskManager)
    {
        $this->taskManager = $taskManager;
    }

    public function publishAction()
    {
        $this->taskManager->publish('test_queue', new TestDTO('testA', 2));

        return new Response('published');
    }

    public function consumeAction()
    {
        $this->taskManager->consume('test_queue', [
            'iterations' => 1
        ]);

        return new Response('consumed');
    }
}