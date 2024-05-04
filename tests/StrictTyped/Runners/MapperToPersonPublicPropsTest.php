<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners;

use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapperToPersonPublicProps;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonGetters;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonPublicProps;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToPersonPublicProps;

class MapperToPersonPublicPropsTest extends TestCase
{
    private MapperToPersonPublicProps $mapperToPersonPublicProps;

    protected function setUp(): void
    {
        parent::setUp();
        $mapper = Mappers::strictTyped();
        $this->mapperToPersonPublicProps = $mapper->getMapper(MapperToPersonPublicProps::class);
    }

    public function testMapFromPersonPublicProps(): void
    {
        $fromPersonPublicProps = new FromPersonPublicProps();
        $fromPersonPublicProps->firstName = 'some name';
        $fromPersonPublicProps->lastName = 'some last name';
        $fromPersonPublicProps->birthDate = '1990-01-12 02:00:00';

        $to = $this->mapperToPersonPublicProps->mapFromPersonPublicProps($fromPersonPublicProps);

        $this->assertSame($fromPersonPublicProps->firstName, $to->firstName);
        $this->assertSame($fromPersonPublicProps->lastName, $to->lastName);
        $this->assertNotNull($to->birth);
        $this->assertSame($fromPersonPublicProps->birthDate, $to->birth->format('Y-m-d H:i:s'));
    }

    public function testMapFromPersonGetters(): void
    {
        $fromPersonGetters = new FromPersonGetters('some name', 'some last name', '1990-01-12 02:00:00');
        $to = $this->mapperToPersonPublicProps->mapFromPersonGetters($fromPersonGetters);

        $this->assertSame($fromPersonGetters->getFirstName(), $to->firstName);
        $this->assertSame($fromPersonGetters->getLastName(), $to->lastName);
        $this->assertNotNull($to->birth);
        $this->assertSame($fromPersonGetters->getBirthDate(), $to->birth->format('Y-m-d H:i:s'));
    }

    public function testMapFromPersonPublicPropsWithTargetReturnNothing(): void
    {
        $fromPersonPublicProps = new FromPersonPublicProps();
        $fromPersonPublicProps->firstName = 'some name';
        $fromPersonPublicProps->lastName = 'some last name';
        $fromPersonPublicProps->birthDate = '1990-01-12 02:00:00';
        $this->mapperToPersonPublicProps->mapFromPersonPublicPropsWithTargetReturnNothing($fromPersonPublicProps, $toPersonPublicProps = new ToPersonPublicProps());

        $this->assertSame($fromPersonPublicProps->firstName, $toPersonPublicProps->firstName);
        $this->assertSame($fromPersonPublicProps->lastName, $toPersonPublicProps->lastName);
        $this->assertNotNull($toPersonPublicProps->birth);
        $this->assertSame($fromPersonPublicProps->birthDate, $toPersonPublicProps->birth->format('Y-m-d H:i:s'));
    }

    public function testMapFromPersonPublicPropsWithTargetReturnVoid(): void
    {
        $fromPersonPublicProps = new FromPersonPublicProps();
        $fromPersonPublicProps->firstName = 'some name';
        $fromPersonPublicProps->lastName = 'some last name';
        $fromPersonPublicProps->birthDate = '1990-01-12 02:00:00';
        $this->mapperToPersonPublicProps->mapFromPersonPublicPropsWithTargetReturnVoid($fromPersonPublicProps, $toPersonPublicProps = new ToPersonPublicProps());

        $this->assertSame($fromPersonPublicProps->firstName, $toPersonPublicProps->firstName);
        $this->assertSame($fromPersonPublicProps->lastName, $toPersonPublicProps->lastName);
        $this->assertNotNull($toPersonPublicProps->birth);
        $this->assertSame($fromPersonPublicProps->birthDate, $toPersonPublicProps->birth->format('Y-m-d H:i:s'));
    }

}
