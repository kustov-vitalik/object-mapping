<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\ManyParams;

use DateTimeImmutable;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Mapping;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\UtilsMappers\DateTimeMapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToPersonConstructor;

#[Mapper]
interface IMapperToPersonConstructorManyParams
{
    public function mapFromArgs(string $firstName, string $lastName, DateTimeImmutable $birth): ToPersonConstructor;

    public function mapFromArgsAndObject(FromSimplePerson $fromSimplePerson, DateTimeImmutable $birth): ToPersonConstructor;

    #[Mapping(target: 'birth', source: 'birthDate')]
    public function mapFromArgsOtherNamed(
        string $firstName,
        string $lastName,
        DateTimeImmutable $birthDate
    ): ToPersonConstructor;

    #[Mapping(target: 'birth', source: 'birthDate', qualifier: new Qualifier('mapDate',DateTimeMapper::class))]
    public function mapFromArgsWithQualifier(
        string $firstName,
        string $lastName,
        string $birthDate
    ): ToPersonConstructor;
}
