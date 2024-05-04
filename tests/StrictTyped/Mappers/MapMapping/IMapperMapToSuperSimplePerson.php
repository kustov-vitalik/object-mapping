<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapMapping;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSuperSimplePerson;

#[Mapper]
interface IMapperMapToSuperSimplePerson
{
    public function fromArray(array $firstName): ToSuperSimplePerson;
}
