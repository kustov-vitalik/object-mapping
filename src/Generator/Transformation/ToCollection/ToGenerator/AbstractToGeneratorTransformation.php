<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToCollection\ToGenerator;

use PhpParser\Node\Expr\Yield_;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\BuilderFactory;

abstract class AbstractToGeneratorTransformation implements ITransformation
{
    public function __construct(protected TFactoryCtx $tFactoryCtx)
    {
    }

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        $builderFactory = new BuilderFactory();
        $mapperMethod = $this->tFactoryCtx->reflectionMethod->getToCollectionMapperMethod();
        if ($mapperMethod->class !== $this->tFactoryCtx->reflectionMethod->class) {
            // external mapper
            $mapperPropertyName = $classBuilder->injectProperty($mapperMethod->class);
            $mapperVar = $builderFactory->propertyFetch($builderFactory->this(), $mapperPropertyName);
        } else {
            // current mapper
            $mapperVar = $builderFactory->this();
        }

        $valueVar = $methodVarScope->createVar('v');
        $keyVar = $methodVarScope->createVar('k');
        $foreach = new Foreach_(
            $expr, $valueVar, [
            'keyVar' => $keyVar,
            'stmts' => [
                new Expression(
                    new Yield_(
                        $builderFactory->methodCall(
                            $mapperVar, $mapperMethod->name, [
                            $builderFactory->argument($valueVar),
                            ]
                        ), $keyVar
                    )
                ),
            ]
            ]
        );

        $method->addStmt($foreach);

        return $expr;
    }
}
