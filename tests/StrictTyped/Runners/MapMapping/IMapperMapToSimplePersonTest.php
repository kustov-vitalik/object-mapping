<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\MapMapping;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapMapping\IMapperMapToSimplePerson;

class IMapperMapToSimplePersonTest extends TestCase
{
    #[Test]
    public function fromArray(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToSimplePerson::class);

        $person = $mapper->fromArray($from = [
            'firstName' => 'fName',
            'lastName' => 'lName',
        ]);

        $this->assertNotNull($person);
        $this->assertSame($from['lastName'], $person->lastName);
        $this->assertSame($from['firstName'], $person->firstName);
    }

    #[Test]
    public function fromArrayWithMappings(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToSimplePerson::class);

        $person = $mapper->fromArrayWithMappings($from = [
            'first' => 'fName',
            'last' => 'lName',
        ]);

        $this->assertNotNull($person);
        $this->assertSame($from['last'], $person->lastName);
        $this->assertSame($from['first'], $person->firstName);
    }
}
