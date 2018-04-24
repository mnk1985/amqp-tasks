<?php namespace App\Amqp\TasksBundle\Tests\Functional;

use App\Amqp\TasksBundle\Manager\TaskManagerInterface;
use App\Amqp\TasksBundle\Tests\Functional\DTO\TestDTO;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class ApplicationTest extends WebTestCase
{

    /**  @var TaskManagerInterface */
    private $taskManager;

    public function setUp()
    {
        $this->taskManager = $this->getContainer()->get(TaskManagerInterface::class);
    }

    public function testCorrectDataPublished()
    {

        $this->taskManager->publish('test_queue', new TestDTO('testA', 2));

        $this->taskManager->consume('test_queue');

    }

    protected function getContainer() {
        return $this->getKernel()->getContainer();
    }

    protected function getKernel(): KernelInterface
    {

        if (static::$kernel) return static::$kernel;

        static::$kernel = static::bootKernel([
            'debug' => false,
        ]);

        return static::$kernel;
    }
}