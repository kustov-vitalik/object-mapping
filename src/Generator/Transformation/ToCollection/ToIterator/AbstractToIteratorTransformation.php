<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToCollection\ToIterator;

use PhpParser\Node\Expr\New_;
use ArrayIterator;
use Iterator;
use IteratorAggregate;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use RuntimeException;
use VKPHPUtils\Mapping\Constants;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\BuilderFactory;

abstract class AbstractToIteratorTransformation implements ITransformation
{
    public function __construct(
        protected TFactoryCtx $tFactoryCtx,
    ) {
    }

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        $reflectionMethod = $this->tFactoryCtx->reflectionMethod->getToCollectionMapperMethod();
        $builderFactory = new BuilderFactory();

        return new New_(
            $this->anonymousIterator($reflectionMethod->class, $reflectionMethod->name), [
            $builderFactory->argument($builderFactory->propertyFetch($builderFactory->this(), Constants::MAPPER_PROPERTY_NAME)),
            $builderFactory->argument($expr),
            ]
        );
    }

    protected function anonymousIterator(string $mapperClassName, string $mapperMethodName): Class_
    {
        $builderFactory = new BuilderFactory();
        return new Class_(
            null, [
            'implements' => [$builderFactory->fullyQualified(Iterator::class)],
            'stmts' => [
                $builderFactory->property('iterator')
                    ->setType($builderFactory->fullyQualified(Iterator::class))
                    ->makePrivate()
                    ->makeReadonly()
                    ->getNode(),

                $builderFactory->property('mapper')
                    ->setType($builderFactory->fullyQualified($mapperClassName))
                    ->makePrivate()
                    ->makeReadonly()
                    ->getNode(),

                $builderFactory->method('__construct')->makePublic()
                    ->addParam($builderFactory->param('mapper')->setType($builderFactory->fullyQualified(Constants::MAPPER_CLASS_NAME)))
                    ->addParam($builderFactory->param('data')->setType($builderFactory->id('iterable')))
                    ->addStmts(
                        [
                        $builderFactory->assign(
                            $builderFactory->propertyFetch($builderFactory->this(), 'mapper'),
                            $builderFactory->methodCall(
                                $builderFactory->var('mapper'),
                                Constants::GET_MAPPER_METHOD_NAME,
                                [
                                    $builderFactory->val($builderFactory->classConstFetch($builderFactory->fullyQualified($mapperClassName), 'class'))
                                ]
                            )
                        ),

                        $builderFactory->if(
                            $builderFactory->instanceOf(
                                $builderFactory->var('data'),
                                $builderFactory->fullyQualified(IteratorAggregate::class)
                            ),
                            [
                                $builderFactory->expression(
                                    $builderFactory->assign(
                                        $builderFactory->propertyFetch($builderFactory->this(), 'iterator'),
                                        $builderFactory->methodCall($builderFactory->var('data'), 'getIterator')
                                    )
                                )
                            ],
                            [
                                new ElseIf_(
                                    $builderFactory->instanceOf(
                                        $builderFactory->var('data'),
                                        $builderFactory->fullyQualified(Iterator::class)
                                    ), [
                                        $builderFactory->expression(
                                            $builderFactory->assign(
                                                $builderFactory->propertyFetch(
                                                    $builderFactory->this(),
                                                    'iterator'
                                                ),
                                                $builderFactory->var('data')
                                            )
                                        )
                                    ]
                                ),
                                new ElseIf_(
                                    $builderFactory->funcCall(
                                        'is_array',
                                        [$builderFactory->argument($builderFactory->var('data'))]
                                    ), [
                                        $builderFactory->expression(
                                            $builderFactory->assign(
                                                $builderFactory->propertyFetch(
                                                    $builderFactory->this(),
                                                    'iterator'
                                                ),
                                                $builderFactory->new(
                                                    $builderFactory->fullyQualified(ArrayIterator::class),
                                                    [$builderFactory->var('data')]
                                                )
                                            )
                                        )
                                    ]
                                )
                            ],
                            new Else_(
                                [
                                $builderFactory->expression(
                                    $builderFactory->throwNewException(
                                        $builderFactory->fullyQualified(RuntimeException::class),
                                        'Invalid data'
                                    )
                                )
                                ]
                            )
                        ),

                        ]
                    )->getNode(),


                $builderFactory->method('current')
                    ->setReturnType($builderFactory->name('mixed'))
                    ->makePublic()
                    ->addStmt(
                        $builderFactory->return(
                            $builderFactory->methodCall(
                                $builderFactory->propertyFetch($builderFactory->this(), 'mapper'),
                                $mapperMethodName,
                                [
                                    $builderFactory->methodCall(
                                        $iteratorVar = $builderFactory->propertyFetch(
                                            $builderFactory->this(),
                                            'iterator'
                                        ),
                                        'current'
                                    )
                                ]
                            )
                        )
                    )->getNode(),

                $builderFactory->method('next')
                    ->setReturnType($builderFactory->name('void'))
                    ->makePublic()
                    ->addStmt($builderFactory->expression($builderFactory->methodCall($iteratorVar, 'next')))
                    ->getNode(),

                $builderFactory->method('key')
                    ->setReturnType($builderFactory->name('mixed'))
                    ->makePublic()
                    ->addStmt($builderFactory->return($builderFactory->methodCall($iteratorVar, 'key')))
                    ->getNode(),

                $builderFactory->method('valid')
                    ->setReturnType($builderFactory->name('bool'))
                    ->makePublic()
                    ->addStmt($builderFactory->return($builderFactory->methodCall($iteratorVar, 'valid')))
                    ->getNode(),

                $builderFactory->method('rewind')
                    ->setReturnType($builderFactory->name('void'))
                    ->makePublic()
                    ->addStmt($builderFactory->expression($builderFactory->methodCall($iteratorVar, 'rewind')))
                    ->getNode(),
            ]
            ]
        );
    }

}
