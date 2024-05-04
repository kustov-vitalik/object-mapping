<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From;

class FromPersonGetters
{
    public function __construct(private readonly string $firstName, private readonly string $lastName, private readonly string $birthDate)
    {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getBirthDate(): string
    {
        return $this->birthDate;
    }
}
