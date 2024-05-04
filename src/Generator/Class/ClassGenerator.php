<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class;

use PhpParser\Node\Stmt\Class_ as ClassStmt;
use VKPHPUtils\Mapping\Generator\Class\Tasks\ConstructorForAbstractClassTask;
use VKPHPUtils\Mapping\Generator\Class\Tasks\ConstructorForInterfaceTask;
use VKPHPUtils\Mapping\Generator\IClassGenerator;
use VKPHPUtils\Mapping\Generator\Tasking\ExecutorTask;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;

final readonly class ClassGenerator implements IClassGenerator
{
    public function __construct(
        private string $mapperFQCN,
        private IMethodGenerator $methodGenerator,
        private ClassBuilder $classBuilder,
    ) {
    }

    public function generateMapperClass(): ClassStmt
    {
        $reflectionClass = ReflectionClass::forName($this->mapperFQCN);

        if ($reflectionClass->isAbstractClass()) {
            $this->classBuilder->extend($reflectionClass->name);
        } elseif ($reflectionClass->isInterface()) {
            $this->classBuilder->implement($reflectionClass->name);
        }

        foreach ($reflectionClass->getMapperMethods() as $reflectionMethod) {
            $this->methodGenerator->generateMapperClassMethod($reflectionMethod);
        }

        (new ExecutorTask(
            new ConstructorForInterfaceTask($reflectionClass, $this->classBuilder),
            new ConstructorForAbstractClassTask($reflectionClass, $this->classBuilder),
        ))->execute();

        return $this->classBuilder->build();
    }
}
