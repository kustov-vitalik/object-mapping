<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapMapping;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Mapping;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSimplePerson;

#[Mapper]
interface IMapperMapToSimplePerson
{
    public function fromArray(array $person): ToSimplePerson;

    #[Mapping(target: 'firstName', source: 'first')]
    #[Mapping(target: 'lastName', source: 'last')]
    public function fromArrayWithMappings(array $person): ToSimplePerson;
}
