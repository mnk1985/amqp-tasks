## Installation

```console
composer require mnk1985/amqp-tasks-bundle
```

add RabbitMQ connection details to .env file. e.g.

```
RABBITMQ_HOST=localhost
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
```

**Note:** As long as you are on Symfony Flex you are done. If not, you have to do some extra things, like registering the bundle in your `AppKernel` class.

## Base components:
- Task - stores queue name in the underlying queue system, knows its handler and dto serializer
- DTO - data structure which stores all the data we need to pass in order to be able to process job asynchronously (e.g. to make async search we can pass some filter details)
- DTOSerializer - is an object which can transform dto to string and vice versa
- TaskHandler - receives dto and does processing (any components can be injected if needed)

UML class diagram may be helpful for visualization
![](Resources/img/uml.png)


## Examples

Task (should implement TaskInterface or extend AbstractTask). getQueueName should return real queue name used to store/retrieve data from queue driver. TestTask::getDTOSerializer returns specific to your task serializer.  TestTask::getHandler returns task's handler. The convention is that handler comes with 'Handler' suffix to task name (e.g. from TestTask we get TestTaskHandler, but it can be overwritten via TestTask::setHandler) 
```php
<?php namespace App\Tasks;

use AmqpTasksBundle\DTO\DTOSerializerInterface;
use AmqpTasksBundle\Tasks\AbstractTask;

class TestTask extends AbstractTask
{
    private const QUEUE_NAME = 'test_queue';

    public function getQueueName(): string
    {
        return self::QUEUE_NAME;
    }

    public function getDTOSerializer(): DTOSerializerInterface
    {
        return new DTOSerializer();
    }
}
```

TaskHandler (should implement TaskhandlerInterface or extend AbstractTaskHandler) - here you can process your task. if it's processed successfully - return true, otherwise - false.

```php
<?php namespace App\Tasks;

use AmqpTasksBundle\Exception\InvalidDTOException;
use AmqpTasksBundle\Tasks\AbstractTaskHandler;

class TestTaskHandler extends AbstractTaskHandler
{
    /**
     * @param TestDTO $message
     * @return bool
     */
    public function process(string $dto): bool
    {
        if (!$dto instanceof TestDTO) {
            throw new InvalidDTOException();
        }

        if ($dto->getFieldA() && $dto->getFieldB()) {
            return true;
        }

        return false;
    }

}
```

Object that gets passed via queue for async processing 
```php
<?php namespace App\Tasks;

class TestDTO
{
    private $fieldA;
    private $fieldB;

    public function __construct($fieldA = null, $fieldB = null)
    {
        $this->fieldA = $fieldA;
        $this->fieldB = $fieldB;
    }

    public function getFieldA(): ?string
    {
        return $this->fieldA;
    }

    public function setFieldA(?string $fieldA): self
    {
        $this->fieldA = $fieldA;

        return $this;
    }

    public function getFieldB(): ?int
    {
        return $this->fieldB;
    }

    public function setFieldB(?int $fieldB): self
    {
        $this->fieldB = $fieldB;

        return $this;
    }
}
```
Serializer should implement DTOSerializerInterface (with 2 methods - convert to string and from string) 
```php
<?php namespace App\Tasks;

use AmqpTasksBundle\DTO\DTOSerializerInterface;
use AmqpTasksBundle\Exception\InvalidDTOException;

class DTOSerializer implements DTOSerializerInterface
{
    /**
         * @param array $dto
         * @return string
         */
        public function convertToString($dto): string
        {
            if (!is_array($dto)) {
                throw new InvalidDTOException('dto should be array');
            }
    
            $dto = new TestDTO(
                $dto['a'] ?? null,
                $dto['b'] ?? null
            );
    
            $fields = [
                'fieldA' => $dto->getFieldA(),
                'fieldB' => $dto->getFieldB(),
            ];
            return json_encode($fields);
        }

    /**
     * @param string $data
     * @return TestDTO
     */
    public function createDTO(string $data)
    {
        $fields = json_decode($data, true);

        $new = new TestDTO();
        $new->setFieldA($fields['fieldA'] ?? null);
        $new->setFieldB($fields['fieldB'] ?? null);

        return $new;
    }
}
```

and controller which will trigger publishing/consuming
```php
<?php namespace App\Controller;

use AmqpTasksBundle\Manager\TaskManagerInterface;
use App\Tasks\TestTask;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TasksController extends Controller
{
    /**
     * @Route("/tasks/publish", name="publish")
     */
    public function publishAction(TaskManagerInterface $manager, TestTask $task)
    {
        $manager->publish($task->getQueueName(), ['a' => 1, 'b' => 2]);

        return new Response('published');
    }

    /**
     * @Route("/tasks/consume", name="consume")
     */
    public function consumeAction(TaskManagerInterface $manager, TestTask $task)
    {
        $manager->consume($task->getQueueName(), [
            'iterations' => 1,
            'attempts' => 2,
            'delay' => 1,
            'verbose' => false,
        ]);

        return new Response('consumed');
    }
}
```

console command for processing tasks. 

```console
./bin/console amqp_tasks:run-worker test_queue --verbose --iterations=100 --attempts=2 --delay=1 --env=dev
```
without --verbose task payload won't be outputted (to console screen or supervisor log)
--iterations=0 (by default) makes worker running "forever" (you may set it to 100. when task is executed as times as iterations are defined, will die, but supervisord will alive it again)
--attempts=2 - make another try if fist processing failed
--delay - delay in seconds when retry after fail

