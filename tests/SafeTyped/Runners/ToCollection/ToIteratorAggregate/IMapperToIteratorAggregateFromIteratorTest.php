<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToCollection\ToIteratorAggregate;

use ArrayIterator;
use IteratorAggregate;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToIteratorAggregate\IMapperToIteratorAggregateFromIterator;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

use function PHPUnit\Framework\assertEquals;

class IMapperToIteratorAggregateFromIteratorTest extends TestCase
{
    #[Test]
    public function mapPeople(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToIteratorAggregateFromIterator::class);

        $fromPeople = [
            new FromPerson('fName1', 'lName1', 22),
            new FromPerson('fName2', 'lName2', 23),
        ];

        $iterator = new ArrayIterator($fromPeople);

        $toPeople = $mapper->mapPeople($iterator);

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
