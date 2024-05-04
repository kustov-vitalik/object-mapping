<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Attributes;

use Attribute;
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class ToDictionary
{
    public function __construct(
        public Qualifier|null $keyMapper = null,
        public Qualifier|null $valueMapper = null,
    ) {
    }
}
