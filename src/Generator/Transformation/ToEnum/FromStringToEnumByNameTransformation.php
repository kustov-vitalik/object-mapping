<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToEnum;

use PhpParser\Builder\Method;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\UnionType;
use ValueError;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionEnum;

class FromStringToEnumByNameTransformation extends FromStringToBackedEnumByNameTransformation
{
    protected function createMapEnumMethod(ReflectionEnum $reflectionEnum, Method $method): void
    {
        $builderFactory = new BuilderFactory();

        $sourceVarName = 'from';
        $param = $builderFactory->param($sourceVarName)
            ->setType(new UnionType([$builderFactory->id('string')]));
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
                        $builderFactory->propertyFetch($valueVar, 'name')
                    ),
                    [
                        $builderFactory->return(
                            $builderFactory->methodCall(
                                $builderFactory->methodCall(
                                    $builderFactory->new(
                                        $builderFactory->fullyQualified(\ReflectionEnum::class),
                                        [
                                            $builderFactory->argument(
                                                $builderFactory->classConstFetch(
                                                    $builderFactory->fullyQualified($reflectionEnum->name),
                                                    'class'
                                                )
                                            )
                                        ]
                                    ),
                                    'getCase',
                                    [$builderFactory->argument($builderFactory->propertyFetch($valueVar, 'name'))]
                                ),
                                'getValue'
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
            'fromStringToEnum_%s_ByName__%s',
            $reflectionEnum->getShortName(),
            md5($reflectionEnum->name)
        );
    }
}
