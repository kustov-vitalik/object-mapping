<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To;

class ToPersonConstructorAndPublicPropsAndSetters
{
    public string $firstName;

    protected string $lastName;

    public function __construct(private readonly string $midName)
    {
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMidName(): string
    {
        return $this->midName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
}
