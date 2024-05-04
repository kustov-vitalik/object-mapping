<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class ToCollection
{
    public function __construct(
        public Qualifier|null $qualifier = null
    ) {
    }
}
