<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToInt;

use PhpParser\Node\Expr\Cast\Int_;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;

abstract class AbstractToIntTransformation implements ITransformation
{

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        return new Int_($expr);
    }
}
