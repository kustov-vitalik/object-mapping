<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To;

interface IToPerson
{
    public function getFirstName(): string;

    public function getLastName(): string;

    public function getAge(): float;
}
