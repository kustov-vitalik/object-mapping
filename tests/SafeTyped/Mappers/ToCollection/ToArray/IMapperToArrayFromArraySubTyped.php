<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToArray;

use JetBrains\PhpStorm\ArrayShape;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\IToPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

#[Mapper]
interface IMapperToArrayFromArraySubTyped
{
    /**
     * @param FromPerson[] $people
     * @return IToPerson[]
     */
    #[ToCollection]
    #[ArrayShape(['people' => "array<FromPerson>"])]
    public function mapPeople(array $people): array;

    public function person(FromPerson $fromPerson): ToPerson;
}
