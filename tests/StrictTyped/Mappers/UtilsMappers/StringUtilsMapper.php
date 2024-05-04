<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\UtilsMappers;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Named;

#[Mapper]
abstract class StringUtilsMapper
{
    public const TO_UPPER_CASE = 'upper';

    #[Named(self::TO_UPPER_CASE)]
    public function toUpperCase(string $string): string
    {
        return mb_strtoupper($string);
    }
}
