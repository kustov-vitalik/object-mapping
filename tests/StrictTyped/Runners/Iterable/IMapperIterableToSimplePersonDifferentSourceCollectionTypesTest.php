<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\Iterable;

use Generator;
use ArrayIterator;
use ArrayObject;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Iterable\IMapperIterableToSimplePersonDifferentSourceCollectionTypes;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToSimplePerson;

class IMapperIterableToSimplePersonDifferentSourceCollectionTypesTest extends TestCase
{
    #[Test]
    public function fromSimplePersonFromArray(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentSourceCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonFromArray($people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonFromIterator(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentSourceCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];


        $toPeople = $mapper->fromSimplePersonFromIterator(new ArrayIterator($people));

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonFromIteratorAggregate(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentSourceCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];


        $toPeople = $mapper->fromSimplePersonFromIteratorAggregate(new ArrayObject($people));

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonFromVariadic(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentSourceCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonFromVariadic(...$people);

        $this->assertNotNull($toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonFromIterable(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentSourceCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonFromIterable($people);

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonFromTraversable(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentSourceCollectionTypes::class);

        $people = [
            new FromSimplePerson(),
            new FromSimplePerson(),
        ];

        $toPeople = $mapper->fromSimplePersonFromTraversable(new ArrayObject($people));

        $this->assertNotNull($toPeople);
        $this->assertCount(count($people), $toPeople);
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }

    #[Test]
    public function fromSimplePersonFromGenerator(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperIterableToSimplePersonDifferentSourceCollectionTypes::class);

        $generator = static function (): Generator {
            yield new FromSimplePerson();
            yield new FromSimplePerson();
        };

        $toPeople = $mapper->fromSimplePersonFromGenerator($generator());

        $this->assertNotNull($toPeople);
        $counter = 0;
        foreach ($toPeople as $toPerson) {
            $this->assertInstanceOf(ToSimplePerson::class, $toPerson);
            ++$counter;
        }

        $this->assertSame(2, $counter);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot rewind a generator that was already run');
        $this->assertContainsOnlyInstancesOf(ToSimplePerson::class, $toPeople);
    }
}
