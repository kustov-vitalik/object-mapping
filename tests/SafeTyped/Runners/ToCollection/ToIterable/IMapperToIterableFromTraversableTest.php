<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToCollection\ToIterable;

use ArrayIterator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToIterable\IMapperToIterableFromTraversable;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

class IMapperToIterableFromTraversableTest extends TestCase
{
    #[Test]
    public function mapPeople(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToIterableFromTraversable::class);

        $fromPeople = [
            new FromPerson('fName1', 'lName1', 22),
            new FromPerson('fName2', 'lName2', 23),
        ];

        $iterator = new ArrayIterator($fromPeople);

        $toPeople = $mapper->mapPeople($iterator);

        $this->assertIsIterable($toPeople);

        foreach ($toPeople as $k => $person) {
            $this->assertInstanceOf(ToPerson::class, $person);
            $this->assertSame($fromPeople[$k]->firstName, $person->firstName);
            $this->assertSame($fromPeople[$k]->lastName, $person->lastName);
            $this->assertEqualsWithDelta($fromPeople[$k]->age, $person->age, 1e-8);
        }
    }
}
