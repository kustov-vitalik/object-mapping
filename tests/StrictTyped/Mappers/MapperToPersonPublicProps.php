<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Mapping;
use VKPHPUtils\Mapping\Attributes\Target;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\UtilsMappers\DateTimeMapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonGetters;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonPublicProps;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToPersonPublicProps;

#[Mapper]
abstract class MapperToPersonPublicProps
{
    #[Mapping(target: 'birth', source: 'birthDate', qualifier: new Qualifier('mapDate',DateTimeMapper::class))]
    abstract public function mapFromPersonPublicPropsWithTargetReturnVoid(
        FromPersonPublicProps $person,
        #[Target] ToPersonPublicProps $targetPerson
    ): void;

    #[Mapping(target: 'birth', source: 'birthDate', qualifier: new Qualifier('mapDate',DateTimeMapper::class))]
    abstract public function mapFromPersonPublicPropsWithTargetReturnNothing(
        FromPersonPublicProps $person,
        #[Target] ToPersonPublicProps $targetPerson
    );

    #[Mapping(target: 'birth', source: 'birthDate', qualifier: new Qualifier('mapDate',DateTimeMapper::class))]
    abstract public function mapFromPersonPublicProps(FromPersonPublicProps $person): ToPersonPublicProps;


    #[Mapping(target: 'birth', source: 'birthDate', qualifier: new Qualifier('mapDate',DateTimeMapper::class))]
    abstract public function mapFromPersonGetters(FromPersonGetters $person): ?ToPersonPublicProps;
}
