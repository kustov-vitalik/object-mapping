<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapMapping;

use VKPHPUtils\Mapping\Attributes\ToDictionary;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Named;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\UtilsMappers\DateTimeMapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\UtilsMappers\StringUtilsMapper;

#[Mapper]
abstract class IMapperMapToArray
{
    private const UPPERCASE = 'upper';

    private const CAPITALISE = 'capitalise';

    #[ToDictionary]
    abstract public function map(array $person): array;

    #[ToDictionary(keyMapper: new Qualifier(self::UPPERCASE))]
    abstract public function mapWithKeyQualifier(array $person): array;

    #[ToDictionary(valueMapper: new Qualifier(self::CAPITALISE))]
    abstract public function mapWithValueQualifier(array $person): array;

    #[ToDictionary(
        keyMapper: new Qualifier(self::UPPERCASE),
        valueMapper: new Qualifier(self::CAPITALISE))
    ]
    abstract public function mapWithKeyAndValueQualifiers(array $person): array;

    #[ToDictionary(valueMapper: new Qualifier('mapDate', DateTimeMapper::class))]
    abstract public function mapWithOuterValueQualifier(array $person): array;

    #[ToDictionary(keyMapper: new Qualifier(StringUtilsMapper::TO_UPPER_CASE, StringUtilsMapper::class))]
    abstract public function mapWithOuterKeyQualifier(array $person): array;

    #[ToDictionary(
        keyMapper: new Qualifier(StringUtilsMapper::TO_UPPER_CASE, StringUtilsMapper::class),
        valueMapper: new Qualifier('mapDate', DateTimeMapper::class))
    ]
    abstract public function mapWithOuterKeyAndValueQualifier(array $person): array;

    #[Named(self::UPPERCASE)]
    protected function uppercase(mixed $val): string
    {
        return strtoupper((string)$val);
    }

    #[Named(self::CAPITALISE)]
    protected function capitalise(mixed $val): string
    {
        return ucfirst((string)$val);
    }
}
