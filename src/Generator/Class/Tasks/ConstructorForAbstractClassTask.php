<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class\Tasks;

use PhpParser\Builder\Class_;
use VKPHPUtils\Mapping\Constants;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Tasking\IOptionalTask;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Helper\ClassASTHolder;
use VKPHPUtils\Mapping\Helper\TypeMapper;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;

final readonly class ConstructorForAbstractClassTask implements IOptionalTask
{
    public function __construct(
        private ReflectionClass $reflectionClass,
        private ClassBuilder $classBuilder,
    ) {
    }

    public function supports(): bool
    {
        return $this->reflectionClass->isAbstractClass();
    }

    public function execute(): void
    {
        $builderFactory = new BuilderFactory();

        $constructor = $this->classBuilder->getConstructor();
        $reflectionConstructor = $this->reflectionClass->getConstructor();
        if ($reflectionConstructor !== null) {
            $args = [];
            foreach ($reflectionConstructor->getParameters() as $reflectionParameter) {
                $args[] = $builderFactory->argument($builderFactory->var($reflectionParameter->name));
                $constructorParam = $builderFactory->param($reflectionParameter->name);
                if ($reflectionParameter->isPassedByReference()) {
                    $constructorParam->makeByRef();
                }

                if ($reflectionParameter->isVariadic()) {
                    $constructorParam->makeVariadic();
                }

                if ($reflectionParameter->isDefaultValueAvailable()) {
                    if ($reflectionParameter->isDefaultValueConstant()) {
                        $constructorParam->setDefault($builderFactory->val($reflectionParameter->getDefaultValueConstantName()));
                    } else {
                        $constructorParam->setDefault($builderFactory->val($reflectionParameter->getDefaultValue()));
                    }
                }

                if ($reflectionParameter->hasType()) {
                    $constructorParam->setType((new TypeMapper())->mapType($reflectionParameter->getType()));
                } else {
                    throw new NotImplementedYet();
                }

                $constructor->addParam($constructorParam);
            }

            $constructor->addStmts(
                [
                $builderFactory->expression(
                    $builderFactory->staticCall(
                        $builderFactory->name('parent'),
                        $builderFactory->id('__construct'),
                        $builderFactory->args($args)
                    )
                )
                ]
            );
        }

        $constructor->addParam(
            $builderFactory->param(Constants::MAPPER_PROPERTY_NAME)
                ->setType($builderFactory->fullyQualified(Constants::MAPPER_CLASS_NAME))
                ->makePrivate()
                ->makeReadonly()
        );
    }
}
