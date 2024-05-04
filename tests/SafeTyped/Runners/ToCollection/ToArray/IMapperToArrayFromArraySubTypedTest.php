<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToCollection\ToArray;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToArray\IMapperToArrayFromArrayNoMapperMethod;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToArray\IMapperToArrayFromArraySubTyped;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

class IMapperToArrayFromArraySubTypedTest extends TestCase
{
    #[Test]
    public function mapPeople(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToArrayFromArraySubTyped::class);

        $fromPeople = [
            new FromPerson('fName1', 'lName1', 22),
            new FromPerson('fName2', 'lName2', 23),
        ];

        $toPeople = $mapper->mapPeople($fromPeople);

        $this->assertCount(count($fromPeople), $toPeople);
        foreach ($toPeople as $k => $person) {
            $this->assertInstanceOf(ToPerson::class, $person);
            $this->assertSame($fromPeople[$k]->firstName, $person->firstName);
            $this->assertSame($fromPeople[$k]->lastName, $person->lastName);
            $this->assertEqualsWithDelta($fromPeople[$k]->age, $person->age, 1e-8);
        }
    }
}
