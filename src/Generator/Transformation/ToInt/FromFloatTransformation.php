<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToInt;

use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;

class FromFloatTransformation implements ITransformation
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
