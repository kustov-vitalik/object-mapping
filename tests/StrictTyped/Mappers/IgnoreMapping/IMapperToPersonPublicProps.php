<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\IgnoreMapping;

use DateTimeImmutable;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Mapping;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Attributes\Target;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToPersonPublicProps;

#[Mapper]
interface IMapperToPersonPublicProps
{
    #[Mapping(target: 'birth', ignore: true)]
    public function fromSimplePerson(FromSimplePerson $person): ToPersonPublicProps;

    #[Mapping(target: 'firstName', ignore: true)]
    #[Mapping(target: 'lastName', ignore: true)]
    #[Mapping(target: 'birth', source: 'bDate')]
    public function fillBirthDate(#[Target] ToPersonPublicProps $person, DateTimeImmutable $bDate): void;

    #[Mapping(target: 'firstName', source: 'name')]
    #[Mapping(target: 'lastName', ignore: true)]
    #[Mapping(target: 'birth', ignore: true)]
    public function fillFirstName(string $name, #[Target] ToPersonPublicProps $person): void;
}
