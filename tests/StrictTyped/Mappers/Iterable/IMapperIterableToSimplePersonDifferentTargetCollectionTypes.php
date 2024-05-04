<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Iterable;

use Iterator;
use IteratorAggregate;
use Generator;
use Traversable;
use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Named;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSimplePerson;

#[Mapper]
interface IMapperIterableToSimplePersonDifferentTargetCollectionTypes
{
    public const PERSON_MAPPER_METHOD = 'mapPerson';

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonToArray(array $peopleFrom): array;

    /**
     * @param FromSimplePerson[] $peopleFrom
     */
    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonToIteratorDocBlock(array $peopleFrom): Iterator;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonToIteratorReflection(array $peopleFrom): Iterator;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonToIteratorAggregate(array $peopleFrom): IteratorAggregate;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonVariadicToGenerator(FromSimplePerson...$people): Generator;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonVariadicToTraversable(FromSimplePerson...$people): Traversable;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonVariadicToIterable(FromSimplePerson...$people): iterable;

    #[Named(self::PERSON_MAPPER_METHOD)]
    public function mapFromSimplePerson(FromSimplePerson $fromSimplePerson): ToSimplePerson;
}
