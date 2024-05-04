<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types;

enum BuiltinType
{
    private const SCALAR_TYPES = [
        self::BOOLEAN->name => self::BOOLEAN,
        self::FALSE->name => self::BOOLEAN,
        self::TRUE->name => self::BOOLEAN,
        self::STRING->name => self::STRING,
        self::INTEGER->name => self::INTEGER,
        self::FLOAT->name => self::FLOAT,
    ];

    case OBJECT;
    case RESOURCE;
    case STRING;

    case INTEGER;
    case FLOAT;

    case BOOLEAN;
    case FALSE;
    case TRUE;
    case NULL;

    case ARRAY;
    case ITERABLE;
    case CALLABLE;

    public function isScalar(): bool
    {
        return isset(self::SCALAR_TYPES[$this->name]);
    }
}
