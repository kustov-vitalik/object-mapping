<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Enums;

enum IntBackedCurrency: int
{
    case USD = 0;
    case MXN = 1;
}
