<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types\Extractor;

use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;
use ReflectionClass;
use ReflectionUnionType;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\RuntimeException;

class ReflectionTypesExtractor implements ITypesExtractor
{
    /**
     * @return Type[]
     */
    public function getMethodReturnTypes(ReflectionMethod $reflectionMethod): array
    {
        if (!$reflectionMethod->hasReturnType()) {
            throw new RuntimeException();
        }

        return $this->extractFromReflectionType(
            $reflectionMethod->getReturnType(),
            $reflectionMethod->getDeclaringClass()
        );
    }

    /**
     * @return Type[]
     */
    public function getParameterTypes(ReflectionParameter $reflectionParameter): array
    {
        if (!$reflectionParameter->hasType()) {
            throw new RuntimeException();
        }

        if (!$reflectionParameter->getDeclaringClass()) {
            throw new RuntimeException();
        }

        return $this->extractFromReflectionType(
            $reflectionParameter->getType(),
            $reflectionParameter->getDeclaringClass()
        );
    }


    private function extractFromReflectionType(ReflectionType $reflectionType, ReflectionClass $reflectionClass): array
    {
        $types = [];
        $nullable = $reflectionType->allowsNull();

        $phpTypes = ($reflectionType instanceof ReflectionUnionType
            || $reflectionType instanceof ReflectionIntersectionType)
            ? $reflectionType->getTypes()
            : [$reflectionType];

        foreach ($phpTypes as $phpType) {
            if (!$phpType instanceof ReflectionNamedType) {
                // Nested composite types are not supported yet.
                return [];
            }

            $phpTypeOrClass = $phpType->getName();
            if ('null' === $phpTypeOrClass) {
                continue;
            }

            if ('mixed' === $phpTypeOrClass) {
                continue;
            }

            if ('never' === $phpTypeOrClass) {
                continue;
            }

            if (Type::BUILTIN_TYPE_ARRAY === $phpTypeOrClass) {
                $types[] = new Type(Type::BUILTIN_TYPE_ARRAY, $nullable, null, true);
            } elseif ('void' === $phpTypeOrClass) {
                $types[] = new Type(Type::BUILTIN_TYPE_NULL, $nullable);
            } elseif ($phpType->isBuiltin()) {
                $types[] = new Type($phpTypeOrClass, $nullable);
            } else {
                $types[] = new Type(
                    Type::BUILTIN_TYPE_OBJECT,
                    $nullable,
                    $this->resolveTypeName($phpTypeOrClass, $reflectionClass)
                );
            }
        }

        return $types;
    }

    private function resolveTypeName(string $name, ReflectionClass $reflectionClass): string
    {
        if ('self' === $lcName = strtolower($name)) {
            return $reflectionClass->name;
        }

        if ('parent' === $lcName && $parent = $reflectionClass->getParentClass()) {
            return $parent->name;
        }

        return $name;
    }

    public function getPropertyTypes(ReflectionProperty $reflectionProperty): array
    {
        if (!$reflectionProperty->hasType()) {
            throw new RuntimeException();
        }

        return $this->extractFromReflectionType(
            $reflectionProperty->getType(),
            $reflectionProperty->getDeclaringClass()
        );
    }
}
