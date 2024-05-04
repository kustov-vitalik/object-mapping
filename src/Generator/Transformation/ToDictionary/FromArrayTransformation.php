<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToDictionary;

use VKPHPUtils\Mapping\Attributes\Qualifier;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Foreach_;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;

class FromArrayTransformation implements ITransformation
{
    public function __construct(
        private readonly ReflectionMethod $reflectionMethod,
    ) {
    }

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        $builderFactory = new BuilderFactory();

        $stmts = [];

        $targetVar = $methodVarScope->getTargetVar();

        if (!$this->reflectionMethod->hasTargetParameter()) {
            $stmts[] = $builderFactory->assign($targetVar, $builderFactory->array());
        }

        $variableForeachKey = $methodVarScope->createVar('k');
        $foreachKey = $variableForeachKey;
        $keyMapper = $this->reflectionMethod->getToDictionary()->keyMapper;
        if ($keyMapper instanceof Qualifier) {
            $mapperMethod = $this->reflectionMethod->getMapperMethod($keyMapper);
            if ($this->reflectionMethod->class === $mapperMethod->class) {
                $mapperVar = $builderFactory->this();
            } else {
                $mapperPropertyName = $classBuilder->injectProperty($mapperMethod->class);
                $mapperVar = $builderFactory->propertyFetch($builderFactory->this(), $mapperPropertyName);
            }

            $foreachKey = $builderFactory->methodCall(
                $mapperVar,
                $mapperMethod->name,
                [$builderFactory->argument($variableForeachKey)]
            );
        }

        $variableForeachValue = $methodVarScope->createVar('v');
        $foreachValue = $variableForeachValue;
        $valueMapper = $this->reflectionMethod->getToDictionary()->valueMapper;
        if ($valueMapper instanceof Qualifier) {
            $mapperMethod = $this->reflectionMethod->getMapperMethod($valueMapper);
            if ($this->reflectionMethod->class === $mapperMethod->class) {
                $mapperVar = $builderFactory->this();
            } else {
                $mapperPropertyName = $classBuilder->injectProperty($mapperMethod->class);
                $mapperVar = $builderFactory->propertyFetch($builderFactory->this(), $mapperPropertyName);
            }

            $foreachValue = $builderFactory->methodCall(
                $mapperVar,
                $mapperMethod->name,
                [$builderFactory->argument($variableForeachValue)]
            );
        }

        $stmts[] = new Foreach_(
            $expr, $variableForeachValue, [
            'stmts' => [
                $builderFactory->expression(
                    $builderFactory->assign(new ArrayDimFetch($targetVar, $foreachKey), $foreachValue)
                ),
            ],
            'keyVar' => $variableForeachKey,
            ]
        );

        $method->addStmts($stmts);


        return $methodVarScope->getTargetVar();
    }
}
