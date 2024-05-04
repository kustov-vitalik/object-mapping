<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Extractor;

enum ReaderType
{
    case ARRAY_DIM;
    case PROPERTY;
    case GETTER;
}
