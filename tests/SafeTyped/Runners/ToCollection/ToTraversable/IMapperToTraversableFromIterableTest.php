<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToCollection\ToTraversable;

use Generator;
use Traversable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToTraversable\IMapperToTraversableFromGenerator;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToCollection\ToTraversable\IMapperToTraversableFromIterable;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

class IMapperToTraversableFromIterableTest extends TestCase
{
    #[Test]
    public function mapPeople(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToTraversableFromIterable::class);

        $fromPeople = [
            new FromPerson('fName1', 'lName1', 22),
            new FromPerson('fName2', 'lName2', 23),
        ];

        $generator = static function() use ($fromPeople) : Generator {
            yield from $fromPeople;
        };

        $toPeople = $mapper->mapPeople($generator());

        $this->assertInstanceOf(Traversable::class, $toPeople);
        $counter = 0;
        foreach ($toPeople as $k => $person) {
            $this->assertInstanceOf(ToPerson::class, $person);
            $this->assertSame($fromPeople[$k]->firstName, $person->firstName);
            $this->assertSame($fromPeople[$k]->lastName, $person->lastName);
            $this->assertEqualsWithDelta($fromPeople[$k]->age, $person->age, PHP_FLOAT_EPSILON);
            ++$counter;
        }

        $this->assertCount($counter, $fromPeople);
    }
}
