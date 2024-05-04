<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To;

use VKPHPUtils\Mapping\Tests\StrictTyped\Enum\StringBackedCurrency;

class ToMoney
{
    public float $amount;

    public StringBackedCurrency $currency;

}
