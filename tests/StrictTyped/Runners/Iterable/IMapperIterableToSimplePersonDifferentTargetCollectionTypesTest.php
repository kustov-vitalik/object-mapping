<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\Iterable;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Iterable\IMapperIterableToSimplePersonDifferentTargetCollectionTypes;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSimplePerson;

class IMapperIterableToSimplePersonDifferentTargetCollectionTypesTest extends TestCase
{
    #[Test]
    public function fromSimplePersonArray(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentTargetCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonToArray($people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonToIteratorDocBlock(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentTargetCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];


        $toPeople = $mapper->fromSimplePersonToIteratorDocBlock($people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonToIteratorReflection(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentTargetCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];


        $toPeople = $mapper->fromSimplePersonToIteratorReflection($people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonVariadicToGenerator(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentTargetCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonVariadicToGenerator(...$people);

        $this->assertNotNull($toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonToIteratorAggregate(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentTargetCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonToIteratorAggregate($people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonVariadicToTraversable(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentTargetCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonVariadicToTraversable(...$people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonVariadicToIterable(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentTargetCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonVariadicToIterable(...$people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }
}
