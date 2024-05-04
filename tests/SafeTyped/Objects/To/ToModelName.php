<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To;

readonly class ToModelName
{
    public function __construct(
        public string $modelName
    )
    {
    }
}
