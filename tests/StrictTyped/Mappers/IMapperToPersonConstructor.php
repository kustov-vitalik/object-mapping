<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Mapping;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\UtilsMappers\DateTimeMapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonGetters;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonPublicProps;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToPersonConstructor;

#[Mapper]
interface IMapperToPersonConstructor
{
    #[Mapping(target: 'birth', source: 'birthDate', qualifier: new Qualifier('mapDate', DateTimeMapper::class))]
    public function mapFromPersonPublicProps(FromPersonPublicProps $person): ToPersonConstructor;


    #[Mapping(target: 'birth', source: 'birthDate', qualifier: new Qualifier('mapDate', DateTimeMapper::class))]
    public function mapFromPersonGetters(FromPersonGetters $person): ?ToPersonConstructor;
}
