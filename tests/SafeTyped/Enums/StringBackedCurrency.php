<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Enums;

enum StringBackedCurrency: string
{
    case USD = 'US dollar';
    case MXN = 'Mexican peso';
}
