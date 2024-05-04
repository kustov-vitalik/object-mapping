<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Enum;

enum StringBackedCurrency: string
{
    case USD = 'USD';
    case MXN = 'MXN';
}
