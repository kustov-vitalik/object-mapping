<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Attributes;

enum MapDictStrategy
{
    case AUTO;
    case FROM_SOURCE;
    case FROM_TARGET;
    case COPY;
}
