<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation;

use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;
use VKPHPUtils\Mapping\Reflection\ReflectionParameter;

final readonly class TFactoryCtx
{
    /**
     * @param Map<string, ReflectionParameter> $map
     */
    public function __construct(
        public Map $map,
        public ReflectionMethod $reflectionMethod,
    ) {
    }


}
