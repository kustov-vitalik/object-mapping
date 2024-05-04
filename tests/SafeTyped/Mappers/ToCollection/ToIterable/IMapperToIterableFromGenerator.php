<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToIterable;

use Generator;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

#[Mapper]
interface IMapperToIterableFromGenerator
{
    /**
     * @param Generator<FromPerson> $people
     * @return iterable<ToPerson>
     */
    #[ToCollection]
    public function mapPeople(Generator $people): iterable;

    public function person(FromPerson $fromPerson): ToPerson;
}
