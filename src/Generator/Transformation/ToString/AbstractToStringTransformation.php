<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToString;

use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Builder\Method;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name\FullyQualified;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;

abstract class AbstractToStringTransformation implements ITransformation
{

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        return new Ternary(
            new FuncCall(new FullyQualified('is_string'), [new Arg($expr)]),
            $expr,
            new String_($expr)
        );
    }
}
