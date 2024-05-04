<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping;

use Psr\Container\ContainerInterface;
use VKPHPUtils\Mapping\Mapper\StrictTypedMapper;

abstract class Mappers implements IMapper
{
    public static function strictTyped(ContainerInterface|null $container = null): IMapper
    {
        return new StrictTypedMapper($container);
    }

    public static function safeTyped(ContainerInterface|null $container = null): IMapper
    {
        return new StrictTypedMapper($container);
    }
}
