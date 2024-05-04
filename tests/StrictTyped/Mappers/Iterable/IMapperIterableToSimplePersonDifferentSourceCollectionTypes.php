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
interface IMapperIterableToSimplePersonDifferentSourceCollectionTypes
{
    public const PERSON_MAPPER_METHOD = 'mapPerson';

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonFromArray(array $peopleFrom): array;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonFromIterator(Iterator $peopleFrom): Iterator;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonFromIteratorAggregate(IteratorAggregate $peopleFrom): IteratorAggregate;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonFromVariadic(FromSimplePerson...$people): Generator;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonFromTraversable(Traversable $people): Traversable;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonFromIterable(iterable $people): iterable;

    #[ToCollection(qualifier: new Qualifier(self::PERSON_MAPPER_METHOD))]
    public function fromSimplePersonFromGenerator(Generator $people): IteratorAggregate;

    #[Named(self::PERSON_MAPPER_METHOD)]
    public function mapFromSimplePerson(FromSimplePerson $fromSimplePerson): ToSimplePerson;
}
