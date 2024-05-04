<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class\Method\Parameter;

use PhpParser\Builder\Param;
use VKPHPUtils\Mapping\Exception\RuntimeException;
use VKPHPUtils\Mapping\Generator\Class\Method\IParameterGenerator;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Helper\TypeMapper;
use VKPHPUtils\Mapping\Reflection\ReflectionParameter;

final readonly class ParameterGenerator implements IParameterGenerator
{

    public function __construct(
        private TypeMapper $typeMapper = new TypeMapper(),
    ) {
    }

    public function generateMapperClassMethodParameter(ReflectionParameter $reflectionParameter): Param
    {
        $builderFactory = new BuilderFactory();
        $parameter = $builderFactory->param($reflectionParameter->getName());

        if ($reflectionParameter->isDefaultValueAvailable()) {
            if ($reflectionParameter->isDefaultValueConstant()) {
                $defaultValueConstantName = $reflectionParameter->getDefaultValueConstantName();
                $parameter->setDefault($builderFactory->id($defaultValueConstantName));
            } else {
                $parameter->setDefault($builderFactory->val($reflectionParameter->getDefaultValue()));
            }
        }

        if ($reflectionParameter->isVariadic()) {
            $parameter->makeVariadic();
        }

        if ($reflectionParameter->isPassedByReference()) {
            $parameter->makeByRef();
        }

        if ($reflectionParameter->isPromoted()) {
            $reflectionClass = $reflectionParameter->getDeclaringClass();
            if ($reflectionClass === null) {
                throw new RuntimeException("No class");
            }

            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                if ($reflectionParameter->name === $reflectionProperty->name && $reflectionProperty->isPromoted()) {
                    if ($reflectionProperty->isReadOnly()) {
                        $parameter->makeReadonly();
                    }

                    if ($reflectionProperty->isPrivate()) {
                        $parameter->makePrivate();
                    } elseif ($reflectionProperty->isProtected()) {
                        $parameter->makeProtected();
                    } elseif ($reflectionProperty->isPublic()) {
                        $parameter->makePublic();
                    }

                    break;
                }
            }
        }

        $parameter->setType($this->typeMapper->mapType($reflectionParameter->getType()));

        return $parameter;
    }
}
