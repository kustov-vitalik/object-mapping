<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Iterable;

use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Target;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Iterable\Dependencies\IMapperToSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSimplePerson;

#[Mapper]
interface IMapperIterableToSimplePersonAutoLookupMapperMethod
{

    /**
     * @param FromSimplePerson[] $peopleFrom
     * @return ToSimplePerson[]
     */
    #[ToCollection]
    public function fromSimplePersonArray(array $peopleFrom): array;

    /**
     * @param FromSimplePerson[] $peopleFrom
     * @param ToSimplePerson[] $toPeople
     */
    #[ToCollection]
    public function fromSimplePersonArrayByRef(array $peopleFrom, #[Target] array &$toPeople): void;

    /**
     * @return ToSimplePerson[]
     */
    #[ToCollection]
    public function fromSimplePersonVariadic(FromSimplePerson...$fromSimplePerson): array;

    public function mapFromSimplePerson(FromSimplePerson $fromSimplePerson): ToSimplePerson;

}
