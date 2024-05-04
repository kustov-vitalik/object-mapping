<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToTraversable;

use Generator;
use Traversable;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

#[Mapper]
interface IMapperToTraversableFromGenerator
{
    #[ToCollection(qualifier: new Qualifier(method: 'person'))]
    public function mapPeople(Generator $people): Traversable;

    public function person(FromPerson $fromPerson): ToPerson;
}
