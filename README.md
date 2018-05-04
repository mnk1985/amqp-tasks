## Installation

```
composer require mnk1985/amqp-tasks-bundle
```

## Examples

Task (should implement TaskInterface or extend AbstractTask). getQueueName should return real queue name used to store/retrieve data from queue driver
```php
<?php namespace App\Test;

use AmqpTasksBundle\Tasks\AbstractTask;

class TestTask extends AbstractTask
{

    public function getQueueName(): string
    {
        return 'test_queue';
    }
}
```

TaskHandler (should implement TaskhandlerInterface or extend AbstractTask) - here you can process your tasks. if it's processed successfully - return true, otherwise - false
```php
<?php namespace App\Test;

use AmqpTasksBundle\Exception\InvalidDTOException;
use AmqpTasksBundle\Tasks\AbstractTaskHandler;

class TestTaskHandler extends AbstractTaskHandler
{

    public function process($message): bool
    {
        $dto = TestDTO::createFromString($message);

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

Object that gets passed via queue for async processing (should implement SerializableDTOInterface)
```php
<?php namespace App\Test;

use AmqpTasksBundle\DTO\SerializableDTOInterface;

class TestDTO implements SerializableDTOInterface
{
    private $fieldA;
    private $fieldB;

    public function __construct($fieldA = null, $fieldB = null)
    {
        $this->fieldA = $fieldA;
        $this->fieldB = $fieldB;
    }

    public function convertToString(): string
    {
        $fields = [
            'fieldA' => $this->fieldA,
            'fieldB' => $this->fieldB,
        ];
        return json_encode($fields);
    }

    public static function createFromString(string $data): SerializableDTOInterface
    {
        $fields = json_decode($data, true);

        $new = new self();
        $new->setFieldA($fields['fieldA'] ?? null);
        $new->setFieldB($fields['fieldB'] ?? null);

        return $new;
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


## Remarks
This code is still under development, and no release is yet ready. Please be patient.