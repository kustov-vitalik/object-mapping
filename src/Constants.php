<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping;

interface Constants
{
    public const MAPPER_CLASS_NAME = IMapper::class;

    public const GET_MAPPER_METHOD_NAME = 'getMapper';

    public const RETURN_VAR_KEY = self::class . 'returnVar';

    public const MAPPER_PROPERTY_NAME = '_mapper_';
}
