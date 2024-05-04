<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class\Tasks;

use VKPHPUtils\Mapping\Constants;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Tasking\IOptionalTask;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;

final readonly class ConstructorForInterfaceTask implements IOptionalTask
{
    public function __construct(
        private ReflectionClass $reflectionClass,
        private ClassBuilder $classBuilder,
    ) {
    }

    public function supports(): bool
    {
        return $this->reflectionClass->isInterface();
    }

    public function execute(): void
    {
        $builderFactory = new BuilderFactory();

        $this->classBuilder->getConstructor()
            ->addParam(
                $builderFactory->param(Constants::MAPPER_PROPERTY_NAME)
                    ->setType($builderFactory->fullyQualified(Constants::MAPPER_CLASS_NAME))
                    ->makePrivate()
                    ->makeReadonly()
            );
    }
}
