<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\Iterable;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Iterable\IMapperIterableUsingExternalMapperToSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSimplePerson;

class IMapperIterableUsingExternalMapperToSimplePersonTest extends TestCase
{
    #[Test]
    public function fromSimplePersonArrayHappyPath(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableUsingExternalMapperToSimplePerson::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonArray($people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonArrayByRefHappyPath(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableUsingExternalMapperToSimplePerson::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = [];

        $mapper->fromSimplePersonArrayByRef($people, $toPeople);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonVariadicHappyPath(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableUsingExternalMapperToSimplePerson::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonVariadic(...$people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }
}
