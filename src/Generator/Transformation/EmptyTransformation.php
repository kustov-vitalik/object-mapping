<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation;

use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;

class EmptyTransformation implements ITransformation
{

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        return $expr;
    }
}
