<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To;

use DateTimeImmutable;

class ToPersonSetters
{
    private string $firstName;

    protected string $lastName;

    private DateTimeImmutable $birth;

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

    public function getBirth(): DateTimeImmutable
    {
        return $this->birth;
    }

    public function setBirth(DateTimeImmutable $birth): void
    {
        $this->birth = $birth;
    }
}
