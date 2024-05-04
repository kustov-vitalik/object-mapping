<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToObject;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr\Variable;
use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\DS\TargetPropertiesHeap;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Helper\ClassASTHolder;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

abstract class AbstractToObjectTransformation implements ITransformation
{
    /**
     * @return Map<string, ReflectionProperty>
     */
    final public function getPropertiesToSet(
        ReflectionMethod $reflectionMethod,
        ReflectionClass $reflectionClass,
        MethodVarScope $methodVarScope
    ): Map {
        $map = new Map();
        foreach (
            new TargetPropertiesHeap(
                $reflectionMethod, ...$reflectionClass->getMapperProperties()
            ) as $property
        ) {
            $mapping = $reflectionMethod->getMapping($property->name);
            if ($mapping->ignore) {
                continue;
            }

            $map->put($property->name, $property);
            $methodVarScope->putTargetProperty($property);
        }

        return $map;
    }


    /**
     * @param Map<string, ReflectionProperty> $map
     */
    protected function constructObjectIfNeededCheckShouldReturn(
        ReflectionMethod $reflectionMethod,
        ReflectionClass $reflectionClass,
        MethodVarScope $methodVarScope,
        Map $map,
        Method $method,
    ): bool {
        if ($reflectionMethod->hasTargetParameter()) {
            return false;
        }

        $classASTHolder = ClassASTHolder::forClass($reflectionClass);
        $constructorAST = $classASTHolder->findConstructorAST();
        $builderFactory = new BuilderFactory();
        $args = [];
        if ($constructorAST instanceof ClassMethod) {
            foreach ($constructorAST->params as $param) {
                $variable = $param->var;
                if (!$variable instanceof Variable) {
                    continue;
                }

                $varName = (string)$variable->name;
                $property = ReflectionProperty::fromClassAndName($reflectionClass->name, $varName);
                if (!$map->containsKey($varName)) {
                    throw new InvalidConfigException(
                        sprintf(
                            "Unknown parameter: %s. Available properties: [%s]",
                            $varName,
                            implode(', ', $map->keys())
                        )
                    );
                }

                $args[] = $builderFactory->argument(
                    $methodVarScope->getTargetPropertyVar($property),
                    $param->byRef,
                    $param->variadic,
                    [],
                    $builderFactory->id($varName)
                );

                $map->remove($varName);
            }
        }

        $method->addStmt(
            $builderFactory->assign(
                $methodVarScope->getTargetVar(),
                $builderFactory->new($builderFactory->fullyQualified($reflectionClass->name), $args)
            )
        );

        return $map->isEmpty();
    }
}
