<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToGenerator;

use IteratorAggregate;
use Generator;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

#[Mapper]
interface IMapperToGeneratorFromIteratorAggregate
{
    #[ToCollection(qualifier: new Qualifier(method: 'person'))]
    public function mapPeople(IteratorAggregate $people): Generator;

    public function person(FromPerson $fromPerson): ToPerson;
}
