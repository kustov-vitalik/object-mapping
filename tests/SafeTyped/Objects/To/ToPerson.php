<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To;

final class ToPerson implements IToPerson
{

    public string $firstName;

    public string $lastName;

    public float $age;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getAge(): float
    {
        return $this->age;
    }

    public function setAge(float $age): void
    {
        $this->age = $age;
    }
}
