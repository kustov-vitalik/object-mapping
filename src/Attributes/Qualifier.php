<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Attributes;

final readonly class Qualifier
{
    /**
     * @param class-string|null $class
     */
    public function __construct(
        public string $method,
        public string|null $class = null,
    ) {
    }
}
