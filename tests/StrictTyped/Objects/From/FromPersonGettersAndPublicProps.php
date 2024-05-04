<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From;

class FromPersonGettersAndPublicProps
{
    public function __construct(private readonly string $firstName, protected string $lastName, public string $midName)
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
}
