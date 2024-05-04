<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\ToEnum;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\ToEnum\IMapperToCurrency;

class IMapperToCurrencyTest extends TestCase
{
    #[Test]
    public function fromCurrencyName(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperToCurrency::class);

        $currency = $mapper->fromCurrencyName($currencyName = 'USD');

        $this->assertNotNull($currency);
        $this->assertSame($currencyName, $currency->value);
    }
}
