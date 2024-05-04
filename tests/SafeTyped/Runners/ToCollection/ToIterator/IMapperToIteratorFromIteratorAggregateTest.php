<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToCollection\ToIterator;

use ArrayObject;
use Iterator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToIterator\IMapperToIteratorFromIterable;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToIterator\IMapperToIteratorFromIteratorAggregate;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

class IMapperToIteratorFromIteratorAggregateTest extends TestCase
{
    #[Test]
    public function mapPeople(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToIteratorFromIteratorAggregate::class);

        $fromPeople = [
            new FromPerson('fName1', 'lName1', 22),
            new FromPerson('fName2', 'lName2', 23),
        ];

        $arrayObject = new ArrayObject($fromPeople);

        $toPeople = $mapper->mapPeople($arrayObject);

        $this->assertInstanceOf(Iterator::class, $toPeople);
        foreach ($toPeople as $k => $person) {
            $this->assertInstanceOf(ToPerson::class, $person);
            $this->assertSame($fromPeople[$k]->firstName, $person->firstName);
            $this->assertSame($fromPeople[$k]->lastName, $person->lastName);
            $this->assertEqualsWithDelta($fromPeople[$k]->age, $person->age, PHP_FLOAT_EPSILON);
        }
    }
}
