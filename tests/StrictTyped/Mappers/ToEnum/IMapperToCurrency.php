<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\ToEnum;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Enum\StringBackedCurrency;

#[Mapper]
interface IMapperToCurrency
{
    public function fromCurrencyName(string $currency): StringBackedCurrency;
}
