<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\IMapperToPersonPublicProps;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonGetters;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonPublicProps;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToPersonPublicProps;

class IMapperToPersonPublicPropsTest extends TestCase
{
    #[Test]
    public function mapFromPersonPublicProps(): void
    {
        $provider = Mappers::strictTyped();
        $mapper = $provider->getMapper(IMapperToPersonPublicProps::class);


        $fromPersonPublicProps = new FromPersonPublicProps();
        $fromPersonPublicProps->firstName = 'some name';
        $fromPersonPublicProps->lastName = 'some last name';
        $fromPersonPublicProps->birthDate = '1990-01-12 02:00:00';

        $to = $mapper->mapFromPersonPublicProps($fromPersonPublicProps);

        $this->assertSame($fromPersonPublicProps->firstName, $to->firstName);
        $this->assertSame($fromPersonPublicProps->lastName, $to->lastName);
        $this->assertNotNull($to->birth);
        $this->assertSame($fromPersonPublicProps->birthDate, $to->birth->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function mapFromPersonGetters(): void
    {
        $provider = Mappers::strictTyped();
        $mapper = $provider->getMapper(IMapperToPersonPublicProps::class);

        $fromPersonGetters = new FromPersonGetters('some name', 'some last name', '1990-01-12 02:00:00');
        $to = $mapper->mapFromPersonGetters($fromPersonGetters);

        $this->assertSame($fromPersonGetters->getFirstName(), $to->firstName);
        $this->assertSame($fromPersonGetters->getLastName(), $to->lastName);
        $this->assertNotNull($to->birth);
        $this->assertSame($fromPersonGetters->getBirthDate(), $to->birth->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function mapFromPersonPublicPropsWithTargetReturnNothing(): void
    {
        $provider = Mappers::strictTyped();
        $mapper = $provider->getMapper(IMapperToPersonPublicProps::class);

        $fromPersonPublicProps = new FromPersonPublicProps();
        $fromPersonPublicProps->firstName = 'some name';
        $fromPersonPublicProps->lastName = 'some last name';
        $fromPersonPublicProps->birthDate = '1990-01-12 02:00:00';
        $mapper->mapFromPersonPublicPropsWithTargetReturnNothing($fromPersonPublicProps, $toPersonPublicProps = new ToPersonPublicProps());

        $this->assertSame($fromPersonPublicProps->firstName, $toPersonPublicProps->firstName);
        $this->assertSame($fromPersonPublicProps->lastName, $toPersonPublicProps->lastName);
        $this->assertNotNull($toPersonPublicProps->birth);
        $this->assertSame($fromPersonPublicProps->birthDate, $toPersonPublicProps->birth->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function mapFromPersonPublicPropsWithTargetReturnVoid(): void
    {
        $provider = Mappers::strictTyped();
        $mapper = $provider->getMapper(IMapperToPersonPublicProps::class);

        $fromPersonPublicProps = new FromPersonPublicProps();
        $fromPersonPublicProps->firstName = 'some name';
        $fromPersonPublicProps->lastName = 'some last name';
        $fromPersonPublicProps->birthDate = '1990-01-12 02:00:00';
        $mapper->mapFromPersonPublicPropsWithTargetReturnVoid($fromPersonPublicProps, $toPersonPublicProps = new ToPersonPublicProps());

        $this->assertSame($fromPersonPublicProps->firstName, $toPersonPublicProps->firstName);
        $this->assertSame($fromPersonPublicProps->lastName, $toPersonPublicProps->lastName);
        $this->assertNotNull($toPersonPublicProps->birth);
        $this->assertSame($fromPersonPublicProps->birthDate, $toPersonPublicProps->birth->format('Y-m-d H:i:s'));
    }

}
