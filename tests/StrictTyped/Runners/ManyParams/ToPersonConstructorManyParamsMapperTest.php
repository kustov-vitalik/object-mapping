<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\ManyParams;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\ManyParams\IMapperToPersonConstructorManyParams;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;

class ToPersonConstructorManyParamsMapperTest extends TestCase
{
    #[Test]
    public function mapFromArgs(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperToPersonConstructorManyParams::class);

        $to = $mapper->mapFromArgs($fName = 'fName', $lName = 'lName', $dateTimeImmutable = new DateTimeImmutable());

        $this->assertNotNull($to);
        $this->assertSame($fName, $to->getFirstName());
        $this->assertSame($lName, $to->getLastName());
        $this->assertEquals($dateTimeImmutable, $to->getBirth());
    }

    #[Test]
    public function mapFromArgsAndObject(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperToPersonConstructorManyParams::class);

        $to = $mapper->mapFromArgsAndObject($fromSimplePerson = new FromSimplePerson(), $dateTimeImmutable = new DateTimeImmutable());

        $this->assertNotNull($to);
        $this->assertSame($fromSimplePerson->firstName, $to->getFirstName());
        $this->assertSame($fromSimplePerson->lastName, $to->getLastName());
        $this->assertEquals($dateTimeImmutable, $to->getBirth());
    }

    #[Test]
    public function mapFromArgsOtherNamed(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperToPersonConstructorManyParams::class);

        $to = $mapper->mapFromArgsOtherNamed($fName = 'fName', $lName = 'lName', $dateTimeImmutable = new DateTimeImmutable());

        $this->assertNotNull($to);
        $this->assertSame($fName, $to->getFirstName());
        $this->assertSame($lName, $to->getLastName());
        $this->assertEquals($dateTimeImmutable, $to->getBirth());
    }

    #[Test]
    public function mapFromArgsWithQualifier(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperToPersonConstructorManyParams::class);

        $to = $mapper->mapFromArgsWithQualifier($fName = 'fName', $lName = 'lName', $birthDate = date('Y-m-d H:i:s'));

        $this->assertNotNull($to);
        $this->assertSame($fName, $to->getFirstName());
        $this->assertSame($lName, $to->getLastName());
        $this->assertSame($birthDate, $to->getBirth()->format('Y-m-d H:i:s'));
    }
}
