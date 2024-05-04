<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To;

use DateTimeImmutable;

class ToPersonConstructor
{
    public function __construct(private readonly string $firstName, private readonly string $lastName, private readonly DateTimeImmutable $birth)
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

    public function getBirth(): DateTimeImmutable
    {
        return $this->birth;
    }
}
