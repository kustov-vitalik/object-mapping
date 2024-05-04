<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Enum;

enum IntBackedCurrency: int
{
    case USD = 1;
    case MXN = 2;
}
