<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToEnum;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\Currency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\IntBackedCurrency;
use VKPHPUtils\Mapping\Tests\SafeTyped\Enums\StringBackedCurrency;

#[Mapper]
interface IMapperToIntBackedCurrency
{
    public function fromCurrency(Currency $currency): IntBackedCurrency;

    public function fromStringBackedCurrency(StringBackedCurrency $stringBackedCurrency): IntBackedCurrency;

    public function fromIntBackedCurrency(IntBackedCurrency $intBackedCurrency): IntBackedCurrency;

    public function fromString(string $currency): IntBackedCurrency;

    public function fromInt(int $currency): IntBackedCurrency;
}
