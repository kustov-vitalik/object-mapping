<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Iterable;

use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Target;
use VKPHPUtils\Mapping\Attributes\Named;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSimplePerson;

#[Mapper]
interface IMapperIterableToSimplePerson
{
    public const PERSON_MAPPER_METHOD = 'mapPerson';

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonArray(array $peopleFrom): array;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonArrayByRef(array $peopleFrom, #[Target] array &$toPeople): void;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonVariadic(FromSimplePerson...$people): array;

    #[Named(self::PERSON_MAPPER_METHOD)]
    public function mapFromSimplePerson(FromSimplePerson $fromSimplePerson): ToSimplePerson;
}
