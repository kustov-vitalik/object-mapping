<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners;

use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapperToPersonSetters;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonGetters;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonPublicProps;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToPersonSetters;

class MapperToPersonSettersTest extends TestCase
{
    private MapperToPersonSetters $mapperToPersonSetters;

    protected function setUp(): void
    {
        parent::setUp();
        $mapper = Mappers::strictTyped();
        $this->mapperToPersonSetters = $mapper->getMapper(MapperToPersonSetters::class);
    }

    public function testMapFromPersonPublicProps(): void
    {
        $fromPersonPublicProps = new FromPersonPublicProps();
        $fromPersonPublicProps->firstName = 'some name';
        $fromPersonPublicProps->lastName = 'some last name';
        $fromPersonPublicProps->birthDate = '1990-01-12 02:00:00';

        $to = $this->mapperToPersonSetters->mapFromPersonPublicProps($fromPersonPublicProps);

        $this->assertNotNull($to);
        $this->assertSame($fromPersonPublicProps->firstName, $to->getFirstName());
        $this->assertSame($fromPersonPublicProps->lastName, $to->getLastName());
        $this->assertNotNull($to->getBirth());
        $this->assertSame($fromPersonPublicProps->birthDate, $to->getBirth()->format('Y-m-d H:i:s'));
    }

    public function testMapFromPersonGetters(): void
    {
        $fromPersonGetters = new FromPersonGetters('some name', 'some last name', '1990-01-12 02:00:00');
        $to = $this->mapperToPersonSetters->mapFromPersonGetters($fromPersonGetters);

        $this->assertNotNull($to);
        $this->assertSame($fromPersonGetters->getFirstName(), $to->getFirstName());
        $this->assertSame($fromPersonGetters->getLastName(), $to->getLastName());
        $this->assertNotNull($to->getBirth());
        $this->assertSame($fromPersonGetters->getBirthDate(), $to->getBirth()->format('Y-m-d H:i:s'));
    }

    public function testMapFromPersonPublicPropsWithTargetReturnNothing(): void
    {
        $fromPersonPublicProps = new FromPersonPublicProps();
        $fromPersonPublicProps->firstName = 'some name';
        $fromPersonPublicProps->lastName = 'some last name';
        $fromPersonPublicProps->birthDate = '1990-01-12 02:00:00';
        $this->mapperToPersonSetters->mapFromPersonPublicPropsWithTargetReturnNothing($fromPersonPublicProps, $toPersonSetters = new ToPersonSetters());

        $this->assertSame($fromPersonPublicProps->firstName, $toPersonSetters->getFirstName());
        $this->assertSame($fromPersonPublicProps->lastName, $toPersonSetters->getLastName());
        $this->assertNotNull($toPersonSetters->getBirth());
        $this->assertSame($fromPersonPublicProps->birthDate, $toPersonSetters->getBirth()->format('Y-m-d H:i:s'));
    }

    public function testMapFromPersonPublicPropsWithTargetReturnVoid(): void
    {
        $fromPersonPublicProps = new FromPersonPublicProps();
        $fromPersonPublicProps->firstName = 'some name';
        $fromPersonPublicProps->lastName = 'some last name';
        $fromPersonPublicProps->birthDate = '1990-01-12 02:00:00';
        $this->mapperToPersonSetters->mapFromPersonPublicPropsWithTargetReturnVoid($fromPersonPublicProps, $toPersonSetters = new ToPersonSetters());

        $this->assertSame($fromPersonPublicProps->firstName, $toPersonSetters->getFirstName());
        $this->assertSame($fromPersonPublicProps->lastName, $toPersonSetters->getLastName());
        $this->assertNotNull($toPersonSetters->getBirth());
        $this->assertSame($fromPersonPublicProps->birthDate, $toPersonSetters->getBirth()->format('Y-m-d H:i:s'));
    }

}
