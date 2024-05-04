<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToEnum;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\Currency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\IntBackedCurrency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\StringBackedCurrency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToEnum\IMapperToIntBackedCurrency;

class IMapperToIntBackedCurrencyTest extends TestCase
{
    #[Test]
    public function fromCurrency(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToIntBackedCurrency::class);

        $currency = $mapper->fromCurrency($from = Currency::USD);

        $this->assertNotNull($currency);
        $this->assertEquals($from->name, $currency->name);
    }

    #[Test]
    public function fromStringBackedCurrency(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToIntBackedCurrency::class);

        $currency = $mapper->fromStringBackedCurrency($from = StringBackedCurrency::USD);

        $this->assertNotNull($currency);
        $this->assertEquals($from->name, $currency->name);
    }

    #[Test]
    public function fromIntBackedCurrency(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToIntBackedCurrency::class);

        $currency = $mapper->fromIntBackedCurrency($from = IntBackedCurrency::USD);

        $this->assertNotNull($currency);
        $this->assertSame($from, $currency);
    }

    #[Test]
    public function fromString(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToIntBackedCurrency::class);

        $currency = $mapper->fromString($from = 'USD');

        $this->assertNotNull($currency);
        $this->assertSame($from, $currency->name);
    }

    #[Test]
    public function fromInt(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToIntBackedCurrency::class);

        $currency = $mapper->fromInt($from = IntBackedCurrency::USD->value);

        $this->assertNotNull($currency);
        $this->assertSame($from, $currency->value);
    }
}
