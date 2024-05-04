<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToEnum;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\Currency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\IntBackedCurrency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\StringBackedCurrency;

#[Mapper]
interface IMapperToCurrency
{
    public function fromCurrency(Currency $currency): Currency;

    public function fromIntBackedCurrency(IntBackedCurrency $intBackedCurrency): Currency;

    public function fromStringBackedCurrency(StringBackedCurrency $stringBackedCurrency): Currency;

    public function fromString(string $currency): Currency;
}
