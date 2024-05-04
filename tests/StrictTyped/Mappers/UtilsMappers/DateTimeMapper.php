<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\UtilsMappers;

use DateTimeImmutable;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Named;

#[Mapper]
abstract class DateTimeMapper
{
    #[Named('mapDate')]
    public function mapFromStringToDateTimeImmutable(string $dateAsString): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dateAsString);
    }
}
