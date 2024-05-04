<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToEnum;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Expr\Throw_;
use ValueError;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionEnum;

abstract class AbstractFromEnumToBackedEnumByNameTransformation implements ITransformation
{
    public function __construct(
        protected string $sourceEnumClassName,
        protected string $targetEnumClassName,
    ) {
    }


    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        $reflectionEnum = ReflectionEnum::forName($this->sourceEnumClassName);
        $targetEnumReflection = ReflectionEnum::forName($this->targetEnumClassName);
        $mapMethodName = $this->getMapMethodName($reflectionEnum, $targetEnumReflection);

        $builderFactory = new BuilderFactory();
        $output = $methodVarScope->createVar('outputEnum');

        $method->addStmt(
            $builderFactory->assign(
                $output,
                $builderFactory->methodCall($builderFactory->this(), $mapMethodName, [$builderFactory->argument($expr)])
            )
        );

        if (!$classBuilder->hasMethod($mapMethodName)) {
            $this->createMapEnumMethod(
                $reflectionEnum,
                $targetEnumReflection,
                $classBuilder->createMethod($mapMethodName)
            );
        }

        return $output;
    }

    protected function createMapEnumMethod(ReflectionEnum $sourceEnum, ReflectionEnum $targetEnum, Method $method): void
    {
        $builderFactory = new BuilderFactory();

        $sourceVarName = 'from';
        $param = $builderFactory->param($sourceVarName)
            ->setType($builderFactory->fullyQualified($sourceEnum->name));
        $method->addParam($param);
        $method->setReturnType($builderFactory->fullyQualified($targetEnum->name));
        $method->makePrivate();

        $stmts = [];

        $sourceVar = new Variable($sourceVarName);
        $valueVar = new Variable('case');
        $stmts[] = new Foreach_(
            $builderFactory->staticCall($builderFactory->fullyQualified($targetEnum->name), 'cases'), $valueVar, [
            'stmts' => [
                $builderFactory->if(
                    new Identical(
                        $builderFactory->propertyFetch($sourceVar, 'name'),
                        $builderFactory->propertyFetch($valueVar, 'name')
                    ),
                    [
                        $builderFactory->return(
                            $builderFactory->staticCall(
                                $builderFactory->fullyQualified($targetEnum->name),
                                'from',
                                [$builderFactory->argument($builderFactory->propertyFetch($valueVar, $builderFactory->id('value')))]
                            )
                        )
                    ]
                )
            ],
            ]
        );

        $stmts[] = new Throw_(
            $builderFactory->new(
                $builderFactory->fullyQualified(ValueError::class), [
                $builderFactory->argument(
                    $builderFactory->funcCall(
                        'sprintf', [
                        $builderFactory->argument(
                            $builderFactory->val('Unable to map %s::%s ("%s") to %s. There is no case "%s" in %s')
                        ),
                        $builderFactory->argument(
                            $builderFactory->val(
                                $builderFactory->classConstFetch($builderFactory->fullyQualified($sourceEnum->name), 'class')
                            )
                        ),
                        $builderFactory->argument($builderFactory->propertyFetch($sourceVar, 'name')),
                        $builderFactory->argument($builderFactory->propertyFetch($sourceVar, 'value')),
                        $builderFactory->argument(
                            $builderFactory->val(
                                $builderFactory->classConstFetch($builderFactory->fullyQualified($targetEnum->name), 'class')
                            )
                        ),
                        $builderFactory->argument($builderFactory->propertyFetch($sourceVar, 'name')),
                        $builderFactory->argument(
                            $builderFactory->val(
                                $builderFactory->classConstFetch($builderFactory->fullyQualified($targetEnum->name), 'class')
                            )
                        ),
                        ]
                    )
                )
                ]
            )
        );

        $method->addStmts($stmts);
    }

    protected function getMapMethodName(ReflectionEnum $source, ReflectionEnum $target): string
    {
        return sprintf(
            'fromEnum_%s_toBackedEnum_%s_ByName__%s',
            $source->getShortName(),
            $target->getShortName(),
            md5($source->name . $target->name)
        );
    }
}
