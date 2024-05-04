<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToDictionary;

use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToDictionary\IMapperToPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\ArrayAccessObject;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;

class IMapperToPersonTest extends TestCase
{

    #[Test]
    public function fromPerson(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToPerson::class);
        $to = $mapper->fromPerson($fromPerson = new FromPerson('fName', 'lName', 33));

        $this->assertNotNull($to);
        $this->assertArrayHasKey('firstName', $to);
        $this->assertSame($fromPerson->firstName, $to['firstName']);
        $this->assertArrayHasKey('lastName', $to);
        $this->assertSame($fromPerson->lastName, $to['lastName']);
        $this->assertArrayHasKey('age', $to);
        $this->assertSame($fromPerson->age, $to['age']);
    }

    #[Test]
    public function fromArray(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArray($from = ['firstName' => 'fName', 'lastName' => 'lName', 'age' => 33]);

        $this->assertNotNull($to);
        $this->assertArrayHasKey('firstName', $to);
        $this->assertSame($from['firstName'], $to['firstName']);
        $this->assertArrayHasKey('lastName', $to);
        $this->assertSame($from['lastName'], $to['lastName']);
        $this->assertArrayHasKey('age', $to);
        $this->assertSame($from['age'], $to['age']);
    }

    #[Test]
    public function fromArrayAccessObject(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToPerson::class);

        $from = ['firstName' => 'fName', 'lastName' => 'lName', 'age' => 33];
        $arrayAccessObject = new ArrayAccessObject();
        $arrayAccessObject['firstName'] = $from['firstName'];
        $arrayAccessObject['lastName'] = $from['lastName'];
        $arrayAccessObject['age'] = $from['age'];
        $to = $mapper->fromArrayAccessObject($arrayAccessObject);

        $this->assertNotNull($to);
        $this->assertArrayHasKey('firstName', $to);
        $this->assertSame($from['firstName'], $to['firstName']);
        $this->assertArrayHasKey('lastName', $to);
        $this->assertSame($from['lastName'], $to['lastName']);
        $this->assertArrayHasKey('age', $to);
        $this->assertSame($from['age'], $to['age']);
    }
}
