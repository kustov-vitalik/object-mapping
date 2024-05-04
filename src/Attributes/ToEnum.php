<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class ToEnum
{
    public function __construct(public MapEnumStrategy $mapEnumStrategy = MapEnumStrategy::AUTO)
    {
    }
}
