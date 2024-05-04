<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Attributes;

enum MapEnumStrategy
{
    case TRY_BY_NAME;
    case TRY_BY_VALUE;
    case TRY_BY_NAME_THEN_BY_VALUE;
    case TRY_BY_VALUE_THEN_BY_NAME;
    case AUTO;
}
