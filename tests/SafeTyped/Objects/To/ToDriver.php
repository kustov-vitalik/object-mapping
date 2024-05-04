<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To;

readonly class ToDriver
{
    public function __construct(private string $name, public string $fatherName)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
