<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToDictionary;

use VKPHPUtils\Mapping\Attributes\Mapping;
use VKPHPUtils\Mapping\Attributes\ToDictionary;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\ArrayAccessObject;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;

#[Mapper]
interface IMapperToPerson
{
    #[ToDictionary]
    public function fromPerson(FromPerson $fromPerson): array;

    #[ToDictionary]
    public function fromArray(array $person): array;

    #[ToDictionary]
    #[Mapping(target: 'firstName')]
    #[Mapping(target: 'lastName')]
    #[Mapping(target: 'age')]
    public function fromArrayAccessObject(ArrayAccessObject $person): array;
}
