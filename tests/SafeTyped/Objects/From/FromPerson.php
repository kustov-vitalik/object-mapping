<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From;

final readonly class FromPerson
{
    public function __construct(public string $firstName, public string $lastName, public int $age)
    {
    }
}
