<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToEnum;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\UnionType;
use ValueError;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionEnum;

abstract class AbstractFromIntOrStringToBackedEnumByValueTransformation implements ITransformation
{
    public function __construct(
        protected string $targetEnumClassName,
    ) {
    }


    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        $reflectionEnum = ReflectionEnum::forName($this->targetEnumClassName);
        $mapMethodName = $this->getMapMethodName($reflectionEnum);

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
                $classBuilder->createMethod($mapMethodName)
            );
        }

        return $output;
    }

    protected function createMapEnumMethod(ReflectionEnum $reflectionEnum, Method $method): void
    {
        $builderFactory = new BuilderFactory();

        $sourceVarName = 'from';
        $param = $builderFactory->param($sourceVarName)
            ->setType(new UnionType([$builderFactory->id('int'), $builderFactory->id('string')]));
        $method->addParam($param);
        $method->setReturnType($builderFactory->fullyQualified($reflectionEnum->name));
        $method->makePrivate();

        $stmts = [];

        $sourceVar = new Variable($sourceVarName);
        $valueVar = new Variable('case');
        $stmts[] = new Foreach_(
            $builderFactory->staticCall($builderFactory->fullyQualified($reflectionEnum->name), 'cases'), $valueVar, [
            'stmts' => [
                $builderFactory->if(
                    new Identical(
                        $sourceVar,
                        $builderFactory->propertyFetch($valueVar, $builderFactory->id('value'))
                    ),
                    [
                        $builderFactory->return(
                            $builderFactory->staticCall(
                                $builderFactory->fullyQualified($reflectionEnum->name),
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
                            $builderFactory->val('Unable to map "%s" to "%s" enum. There is no case with value "%s" in %s')
                        ),
                        $builderFactory->argument($sourceVar),
                        $builderFactory->argument(
                            $builderFactory->classConstFetch($builderFactory->fullyQualified($reflectionEnum->name), 'class')
                        ),
                        $builderFactory->argument($sourceVar),
                        $builderFactory->argument(
                            $builderFactory->classConstFetch($builderFactory->fullyQualified($reflectionEnum->name), 'class')
                        ),
                        ]
                    )
                )
                ]
            )
        );

        $method->addStmts($stmts);
    }

    protected function getMapMethodName(ReflectionEnum $reflectionEnum): string
    {
        return sprintf(
            'fromIntOrStringToEnum_%s_ByValue__%s',
            $reflectionEnum->getShortName(),
            md5($reflectionEnum->name)
        );
    }
}
