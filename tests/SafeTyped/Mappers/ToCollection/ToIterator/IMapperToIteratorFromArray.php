<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToIterator;

use Iterator;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

#[Mapper]
interface IMapperToIteratorFromArray
{
    #[ToCollection(qualifier: new Qualifier(method: 'person'))]
    public function mapPeople(array $people): Iterator;

    public function person(FromPerson $fromPerson): ToPerson;
}
