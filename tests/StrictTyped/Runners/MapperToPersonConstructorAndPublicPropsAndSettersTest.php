<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapperToPersonConstructorAndPublicPropsAndSetters;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonGettersAndPublicProps;

class MapperToPersonConstructorAndPublicPropsAndSettersTest extends TestCase
{
    #[Test]
    public function map(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(MapperToPersonConstructorAndPublicPropsAndSetters::class);
        $fromPersonGettersAndPublicProps = new FromPersonGettersAndPublicProps('fName', 'lName', 'mName');
        $to = $mapper->map($fromPersonGettersAndPublicProps);

        $this->assertNotNull($to);
        $this->assertSame($fromPersonGettersAndPublicProps->getFirstName(), $to->firstName);
        $this->assertSame($fromPersonGettersAndPublicProps->getLastName(), $to->getLastName());
        $this->assertSame($fromPersonGettersAndPublicProps->midName, $to->getMidName());
    }
}
