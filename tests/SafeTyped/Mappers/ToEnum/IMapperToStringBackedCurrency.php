<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToEnum;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\Currency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\IntBackedCurrency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\StringBackedCurrency;

#[Mapper]
interface IMapperToStringBackedCurrency
{
    public function fromCurrency(Currency $currency): StringBackedCurrency;

    public function fromStringBackedCurrency(StringBackedCurrency $stringBackedCurrency): StringBackedCurrency;

    public function fromIntBackedCurrency(IntBackedCurrency $intBackedCurrency): StringBackedCurrency;

    public function fromString(string $currency): StringBackedCurrency;
}
