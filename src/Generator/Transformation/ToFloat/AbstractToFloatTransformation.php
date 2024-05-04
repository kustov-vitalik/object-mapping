<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToFloat;

use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;

abstract class AbstractToFloatTransformation implements ITransformation
{

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        return new Double(
            $expr, [
            'kind' => Double::KIND_FLOAT,
            ]
        );
    }
}
