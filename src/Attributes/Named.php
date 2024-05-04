<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Attributes;

use Attribute;
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class Named
{
    public function __construct(
        public string|null $name = null,
    ) {
    }
}
