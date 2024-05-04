<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToCollection\ToArray;

use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Foreach_;
use VKPHPUtils\Mapping\Exception\MapperMethodNotFoundException;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\BuilderFactory;

abstract class AbstractToArrayTransformation implements ITransformation
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

        try {
            $mapperMethod = $this->tFactoryCtx->reflectionMethod->getToCollectionMapperMethod();

            if ($mapperMethod->class !== $this->tFactoryCtx->reflectionMethod->class) {
                // external mapper
                $mapperPropertyName = $classBuilder->injectProperty($mapperMethod->class);
                $mapperVar = $builderFactory->propertyFetch($builderFactory->this(), $mapperPropertyName);
            } else {
                // current mapper
                $mapperVar = $builderFactory->this();
            }

            $mapperMethodName = $mapperMethod->name;
        } catch (MapperMethodNotFoundException $mapperMethodNotFoundException) {
            $mapperMethodName = $classBuilder->generateMapperMethod($mapperMethodNotFoundException->sourceType, $mapperMethodNotFoundException->targetType);
            $mapperVar = $builderFactory->this();
        }


        $output = $methodVarScope->getTargetVar();
        if (!$this->tFactoryCtx->reflectionMethod->hasTargetParameter()) {
            $method->addStmt($builderFactory->assign($output, $builderFactory->array()));
        }

        $item = $methodVarScope->createVar('item');
        $foreach = new Foreach_(
            $expr, $item, [
            'stmts' => [
                $builderFactory->expression(
                    $builderFactory->appendToArray(
                        $output,
                        $builderFactory->methodCall($mapperVar, $mapperMethodName, [$builderFactory->argument($item)])
                    )
                ),
            ]
            ]
        );
        $method->addStmt($foreach);

        return $output;
    }


}
