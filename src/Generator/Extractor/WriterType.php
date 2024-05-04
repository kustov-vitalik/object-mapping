<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Extractor;

enum WriterType
{
    case SETTER;
    case PROPERTY;
    case ARRAY_DIM;
    case CONSTRUCTOR;
    case ADDER_REMOVER;
}
