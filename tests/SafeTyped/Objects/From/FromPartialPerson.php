<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From;

final readonly class FromPartialPerson
{
    public function __construct(public string $firstName, public string $lastName)
    {
    }
}
