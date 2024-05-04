<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToIterable;

use Traversable;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

#[Mapper]
interface IMapperToIterableFromTraversable
{
    /**
     * @param Traversable<FromPerson> $traversable
     * @return iterable<ToPerson>
     */
    #[ToCollection]
    public function mapPeople(Traversable $traversable): iterable;

    public function person(FromPerson $fromPerson): ToPerson;
}
