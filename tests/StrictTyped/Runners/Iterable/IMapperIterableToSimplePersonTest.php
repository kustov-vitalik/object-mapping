<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\Iterable;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Iterable\IMapperIterableToSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSimplePerson;

class IMapperIterableToSimplePersonTest extends TestCase
{
    #[Test]
    public function fromSimplePersonArrayHappyPath(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePerson::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonArray($people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        foreach ($toPeople as $k => $toPerson) {
            $this->assertInstanceOf(ToSimplePerson::class, $toPerson);
            $this->assertSame($people[$k]->firstName, $toPerson->firstName);
            $this->assertSame($people[$k]->lastName, $toPerson->lastName);
        }
    }

    #[Test]
    public function fromSimplePersonArrayByRefHappyPath(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePerson::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = [];

        $mapper->fromSimplePersonArrayByRef($people, $toPeople);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        foreach ($toPeople as $k => $toPerson) {
            $this->assertInstanceOf(ToSimplePerson::class, $toPerson);
            $this->assertSame($people[$k]->firstName, $toPerson->firstName);
            $this->assertSame($people[$k]->lastName, $toPerson->lastName);
        }

        $mapper->fromSimplePersonArrayByRef($people, $toPeople);
        $this->assertCount(count($people) * 2, $toPeople);
    }

    #[Test]
    public function fromSimplePersonVariadicHappyPath(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePerson::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonVariadic(...$people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        foreach ($toPeople as $k => $toPerson) {
            $this->assertInstanceOf(ToSimplePerson::class, $toPerson);
            $this->assertSame($people[$k]->firstName, $toPerson->firstName);
            $this->assertSame($people[$k]->lastName, $toPerson->lastName);
        }
    }
}
