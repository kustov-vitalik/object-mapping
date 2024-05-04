<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Iterable\Dependencies;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Named;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSimplePerson;

#[Mapper]
interface IMapperToSimplePerson
{
    public const PERSON_MAPPER_METHOD = 'mapPerson';

    #[Named(self::PERSON_MAPPER_METHOD)]
    public function mapFromSimplePerson(FromSimplePerson $fromSimplePerson): ToSimplePerson;
}
