<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\InitiationCode;

use ReflectionNamedType;
use Psr\Container\ContainerInterface;
use VKPHPUtils\Mapping\Constants;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\RuntimeException;
use VKPHPUtils\Mapping\Generator\IInitiationCodeGenerator;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;

class InitiationCodeGeneratorFromLoadedClass implements IInitiationCodeGenerator
{

    public function generateInitiationCode(string $instanceClassName): array
    {
        if (!class_exists($instanceClassName)) {
            throw new RuntimeException(sprintf("Class '%s' does not exist.", $instanceClassName));
        }

        $reflectionClass = ReflectionClass::forName($instanceClassName);
        $builderFactory = new BuilderFactory();
        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return [
                $builderFactory->expression(
                    $builderFactory->assign(
                        $builderFactory->var('instance'),
                        $builderFactory->new($instanceClassName)
                    )
                )
            ];
        }


        $args = [];
        foreach ($constructor->getParameters() as $reflectionParameter) {
            $paramName = $reflectionParameter->name;

            if ($paramName === Constants::MAPPER_PROPERTY_NAME) {
                $args[] = $builderFactory->argument(
                    $builderFactory->var('this'),
                    $reflectionParameter->isPassedByReference(),
                    $reflectionParameter->isVariadic(),
                    [],
                    $builderFactory->id($paramName)
                );

                continue;
            }

            if (!$reflectionParameter->hasType()) {
                throw new NotImplementedYet();
            }

            $type = $reflectionParameter->getType();

            if (!$type instanceof ReflectionNamedType) {
                throw new NotImplementedYet();
            }

            $serviceId = $type->getName();

            $args[] = $builderFactory->argument(
                $builderFactory->methodCall(
                    $builderFactory->propertyFetch($builderFactory->var('this'), 'container'),
                    'get',
                    [$builderFactory->val($serviceId)]
                ),
                $reflectionParameter->isPassedByReference(),
                $reflectionParameter->isVariadic(),
                [],
                $builderFactory->id($paramName)
            );
        }


        $stmts = [];


        if (count($args) > 1) {
            $stmts[] = $builderFactory->if(
                $builderFactory->not(
                    $builderFactory->instanceOf(
                        $builderFactory->propertyFetch($builderFactory->var('this'), 'container'),
                        $builderFactory->name(ContainerInterface::class)
                    )
                ),
                [
                    $builderFactory->expression(
                        $builderFactory->throwNewException(
                            \RuntimeException::class,
                            "DI Container should be provided"
                        )
                    )
                ]
            );
        }

        $stmts[] = $builderFactory->expression(
            $builderFactory->assign(
                $builderFactory->var('instance'),
                $builderFactory->new($instanceClassName, $args)
            )
        );

        return $stmts;
    }
}
