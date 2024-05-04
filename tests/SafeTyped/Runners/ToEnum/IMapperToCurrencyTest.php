<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToEnum;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\Currency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\IntBackedCurrency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\StringBackedCurrency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToEnum\IMapperToCurrency;

class IMapperToCurrencyTest extends TestCase
{
    #[Test]
    public function fromCurrency(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToCurrency::class);

        $currency = $mapper->fromCurrency($from = Currency::USD);

        $this->assertNotNull($currency);
        $this->assertSame($from, $currency);
    }


    #[Test]
    public function fromIntBackedCurrency(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToCurrency::class);

        $currency = $mapper->fromIntBackedCurrency($from = IntBackedCurrency::USD);

        $this->assertNotNull($currency);
        $this->assertEquals($from->name, $currency->name);
    }


    #[Test]
    public function fromStringBackedCurrency(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToCurrency::class);

        $currency = $mapper->fromStringBackedCurrency($from = StringBackedCurrency::USD);

        $this->assertNotNull($currency);
        $this->assertEquals($from->name, $currency->name);
    }

    #[Test]
    public function fromString(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToCurrency::class);

        $currency = $mapper->fromString($from = 'USD');

        $this->assertNotNull($currency);
        $this->assertSame($from, $currency->name);
    }
}
