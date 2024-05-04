<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class;

use PhpParser\Builder\Method;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;

interface IMethodGenerator
{
    public function generateMapperClassMethod(ReflectionMethod $reflectionMethod): Method;
}
