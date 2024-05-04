<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToEnum;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Throw_;
use ValueError;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionEnum;

abstract class AbstractFromEnumToEnumByNameTransformation extends AbstractFromEnumToBackedEnumByNameTransformation
{
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
                            $builderFactory->methodCall(
                                $builderFactory->methodCall(
                                    $builderFactory->new(
                                        $builderFactory->fullyQualified(\ReflectionEnum::class),
                                        [
                                            $builderFactory->argument(
                                                $builderFactory->classConstFetch(
                                                    $builderFactory->fullyQualified($targetEnum->name),
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

        $stmts[] = new Expr\Throw_(
            $builderFactory->new(
                $builderFactory->fullyQualified(ValueError::class), [
                $builderFactory->argument(
                    $builderFactory->funcCall(
                        'sprintf', [
                        $builderFactory->argument(
                            $builderFactory->val('Unable to map %s::%s to %s. There is no case "%s" in %s')
                        ),
                        $builderFactory->argument(
                            $builderFactory->val(
                                $builderFactory->classConstFetch($builderFactory->fullyQualified($sourceEnum->name), 'class')
                            )
                        ),
                        $builderFactory->argument($builderFactory->propertyFetch($sourceVar, 'name')),
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
            'fromEnum_%s_toEnum_%s_ByName__%s',
            $source->getShortName(),
            $target->getShortName(),
            md5($source->name . $target->name)
        );
    }
}
