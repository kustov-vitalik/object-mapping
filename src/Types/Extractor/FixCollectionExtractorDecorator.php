<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types\Extractor;

use ReflectionMethod;
use ReflectionParameter;
use Traversable;
use stdClass;
use ReflectionProperty;
use Symfony\Component\PropertyInfo\Type;

readonly class FixCollectionExtractorDecorator implements ITypesExtractor
{
    public function __construct(private ITypesExtractor $typesExtractor)
    {
    }

    public function getMethodReturnTypes(ReflectionMethod $reflectionMethod): array
    {
        $types = $this->typesExtractor->getMethodReturnTypes($reflectionMethod);
        $this->fixTypes($types);
        return $types;
    }

    public function getParameterTypes(ReflectionParameter $reflectionParameter): array
    {
        $types = $this->typesExtractor->getParameterTypes($reflectionParameter);
        $this->fixTypes($types);
        if ($reflectionParameter->isVariadic()) {
            $this->fixVariadic($types);
        }

        return $types;
    }

    private function fixTypes(array &$types): void
    {
        foreach ($types as &$type) {
            $class = $type->getClassName();
            if ($class !== null && (is_subclass_of($class, Traversable::class) || $class === Traversable::class)) {
                $type = new Type(
                    $type->getBuiltinType(),
                    $type->isNullable(),
                    $type->getClassName(),
                    true,
                    $type->getCollectionKeyTypes(),
                    $type->getCollectionValueTypes()
                );
            }

            if ($type->getBuiltinType() === Type::BUILTIN_TYPE_ITERABLE) {
                $type = new Type(
                    $type->getBuiltinType(),
                    $type->isNullable(),
                    $type->getClassName(),
                    true,
                    $type->getCollectionKeyTypes(),
                    $type->getCollectionValueTypes()
                );
            }

            if ($class !== null) {
                continue;
            }

            if ($type->getBuiltinType() !== Type::BUILTIN_TYPE_OBJECT) {
                continue;
            }

            $type = new Type(
                $type->getBuiltinType(),
                $type->isNullable(),
                stdClass::class,
                $type->isCollection(),
                $type->getCollectionKeyTypes(),
                $type->getCollectionValueTypes(),
            );
        }
    }

    public function getPropertyTypes(ReflectionProperty $reflectionProperty): array
    {
        $types = $this->typesExtractor->getPropertyTypes($reflectionProperty);
        $this->fixTypes($types);
        return $types;
    }

    private function fixVariadic(array &$types): void
    {
        foreach ($types as $key => $type) {
            $types[$key] = new Type(
                builtinType: Type::BUILTIN_TYPE_ARRAY,
                nullable: false,
                class: null,
                collection: true,
                collectionKeyType: [
                    new Type(
                        builtinType: Type::BUILTIN_TYPE_INT,
                        nullable: false,
                        class: null,
                        collection: false,
                        collectionKeyType: [],
                        collectionValueType: []
                    )
                ],
                collectionValueType: [$type]
            );
        }
    }
}
