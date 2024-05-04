<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonGettersAndPublicProps;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToPersonConstructorAndPublicPropsAndSetters;

#[Mapper]
interface MapperToPersonConstructorAndPublicPropsAndSetters
{
    public function map(FromPersonGettersAndPublicProps $fromPersonGettersAndPublicProps): ToPersonConstructorAndPublicPropsAndSetters;
}
