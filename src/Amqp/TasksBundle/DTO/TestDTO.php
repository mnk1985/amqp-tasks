<?php namespace App\Amqp\TasksBundle\DTO;

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