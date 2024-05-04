<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToCollection\ToIteratorAggregate;

use Generator;
use IteratorAggregate;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToIteratorAggregate\IMapperToIteratorAggregateFromArray;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToIteratorAggregate\IMapperToIteratorAggregateFromGenerator;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

use function PHPUnit\Framework\assertEquals;

class IMapperToIteratorAggregateFromGeneratorTest extends TestCase
{
    #[Test]
    public function mapPeople(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToIteratorAggregateFromGenerator::class);

        $fromPeople = [
            new FromPerson('fName1', 'lName1', 22),
            new FromPerson('fName2', 'lName2', 23),
        ];

        $generator = static function () use ($fromPeople): Generator {
            yield from $fromPeople;
        };

        $toPeople = $mapper->mapPeople($generator());

        $this->assertInstanceOf(IteratorAggregate::class, $toPeople);

        //check can iterate many times
        $counter = 0;
        foreach ($toPeople as $k => $person) {
            $this->assertInstanceOf(ToPerson::class, $person);
            $this->assertSame($fromPeople[$k]->firstName, $person->firstName);
            $this->assertSame($fromPeople[$k]->lastName, $person->lastName);
            $this->assertEqualsWithDelta($fromPeople[$k]->age, $person->age, PHP_FLOAT_EPSILON);
            ++$counter;
        }

        assertEquals(count($fromPeople), $counter);

    }
}