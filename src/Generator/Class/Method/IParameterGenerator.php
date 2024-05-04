<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class\Method;

use PhpParser\Builder\Param;
use VKPHPUtils\Mapping\Reflection\ReflectionParameter;

interface IParameterGenerator
{
    public function generateMapperClassMethodParameter(ReflectionParameter $reflectionParameter): Param;
}
