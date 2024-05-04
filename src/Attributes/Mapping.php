<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Attributes;

use Attribute;
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Mapping
{
    private const DEFAULT_PRIORITY = 1024;

    public readonly string $source;

    public function __construct(
        public readonly string $target,
        string|null $source = null,
        public readonly int $priority = self::DEFAULT_PRIORITY,
        public readonly bool $ignore = false,
        public readonly Qualifier|null $qualifier = null,
    ) {
        $this->source = $source ?? $target;
    }
}
