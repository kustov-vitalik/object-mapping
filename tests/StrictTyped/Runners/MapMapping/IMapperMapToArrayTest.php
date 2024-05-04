<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\MapMapping;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapMapping\IMapperMapToArray;

class IMapperMapToArrayTest extends TestCase
{
    #[Test]
    public function map(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToArray::class);

        $to = $mapper->map($from = [
            'name' => 'Name',
            'age' => 33,
        ]);

        $this->assertNotNull($to);
        $this->assertArrayHasKey('name', $to);
        $this->assertArrayHasKey('age', $to);
        $this->assertSame($from['name'], $to['name']);
        $this->assertSame($from['age'], $to['age']);
    }

    #[Test]
    public function mapWithKeyQualifier(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToArray::class);

        $to = $mapper->mapWithKeyQualifier($from = [
            'name' => 'Name',
            'age' => 33,
        ]);

        $this->assertNotNull($to);
        $this->assertArrayHasKey('NAME', $to);
        $this->assertArrayHasKey('AGE', $to);
        $this->assertSame($from['name'], $to['NAME']);
        $this->assertSame($from['age'], $to['AGE']);
    }

    #[Test]
    public function mapWithValueQualifier(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToArray::class);

        $to = $mapper->mapWithValueQualifier($from = [
            'name' => 'name',
            'age' => 33,
        ]);

        $this->assertNotNull($to);
        $this->assertArrayHasKey('name', $to);
        $this->assertArrayHasKey('age', $to);
        $this->assertSame('Name', $to['name']);
        $this->assertSame('33', $to['age']);
    }

    #[Test]
    public function mapWithKeyAndValueQualifiers(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToArray::class);

        $to = $mapper->mapWithKeyAndValueQualifiers($from = [
            'name' => 'name',
            'age' => 33,
        ]);

        $this->assertNotNull($to);
        $this->assertArrayHasKey('NAME', $to);
        $this->assertArrayHasKey('AGE', $to);
        $this->assertSame('Name', $to['NAME']);
        $this->assertSame('33', $to['AGE']);
    }

    #[Test]
    public function mapWithOuterKeyQualifier(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToArray::class);

        $to = $mapper->mapWithOuterKeyQualifier($from = [
            'date' => date('Y-m-d H:i:s'),
        ]);

        $this->assertNotNull($to);
        $this->assertArrayHasKey('DATE', $to);
        $this->assertSame($from['date'], $to['DATE']);
    }

    #[Test]
    public function mapWithOuterValueQualifier(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToArray::class);

        $to = $mapper->mapWithOuterValueQualifier($from = [
            'date' => date('Y-m-d H:i:s'),
        ]);

        $this->assertNotNull($to);
        $this->assertArrayHasKey('date', $to);
        $this->assertEquals(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $from['date']), $to['date']);

    }

    #[Test]
    public function mapWithOuterKeyAndValueQualifier(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToArray::class);

        $to = $mapper->mapWithOuterKeyAndValueQualifier($from = [
            'date' => date('Y-m-d H:i:s'),
        ]);

        $this->assertNotNull($to);
        $this->assertArrayHasKey('DATE', $to);
        $this->assertEquals(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $from['date']), $to['DATE']);
    }
}
