<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Helper;

use UnitEnum;
use BackedEnum;
use ArrayAccess;
use Traversable;
use IteratorAggregate;
use Generator;
use Iterator;
use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Reflection\ReflectionEnum;

final class TypeChecker
{
    public static function isEnumType(Type $type, bool $backed = false): bool
    {
        if (Type::BUILTIN_TYPE_OBJECT !== $type->getBuiltinType()) {
            return false;
        }

        $className = $type->getClassName();
        if ($className === null || $className === '' || $className === '0') {
            return false;
        }

        if (!is_subclass_of($className, UnitEnum::class)) {
            return false;
        }

        return !($backed && !is_subclass_of($className, BackedEnum::class));
    }

    public static function isIntBackedEnumType(Type $type): bool
    {
        if (!self::isEnumType($type, true)) {
            return false;
        }

        $className = $type->getClassName();
        if ($className === null || $className === '' || $className === '0') {
            return false;
        }

        return ReflectionEnum::forName($className)->isIntBacked();
    }

    public static function isStringBackedEnumType(Type $type): bool
    {
        if (!self::isEnumType($type, true)) {
            return false;
        }

        $className = $type->getClassName();
        if ($className === null || $className === '' || $className === '0') {
            return false;
        }

        return ReflectionEnum::forName($className)->isStringBacked();
    }

    public static function isObjectType(Type $type): bool
    {
        if (!\in_array($type->getBuiltinType(), [Type::BUILTIN_TYPE_OBJECT, Type::BUILTIN_TYPE_ARRAY], true)) {
            return false;
        }

        if (Type::BUILTIN_TYPE_ARRAY === $type->getBuiltinType() && $type->isCollection()) {
            return false;
        }

        $className = $type->getClassName();
        if ($className === null || $className === '' || $className === '0') {
            return false;
        }

        return !is_subclass_of($className, UnitEnum::class);
    }

    public static function isArrayAccessType(Type $type): bool
    {
        if ($type->getBuiltinType() !== Type::BUILTIN_TYPE_OBJECT) {
            return false;
        }

        $className = $type->getClassName();
        if ($className === null || $className === '' || $className === '0') {
            return false;
        }

        return is_subclass_of($className, ArrayAccess::class);
    }

    public static function isExactlyTraversableType(Type $type): bool
    {
        if ($type->getBuiltinType() !== Type::BUILTIN_TYPE_OBJECT) {
            return false;
        }

        $className = $type->getClassName();
        if ($className === null || $className === '' || $className === '0') {
            return false;
        }

        return $className === Traversable::class;
    }

    public static function isIteratorAggregateType(Type $type): bool
    {
        if ($type->getBuiltinType() !== Type::BUILTIN_TYPE_OBJECT) {
            return false;
        }

        $className = $type->getClassName();
        if ($className === null || $className === '' || $className === '0') {
            return false;
        }

        return $className === IteratorAggregate::class || is_subclass_of($className, IteratorAggregate::class);
    }

    public static function isGeneratorType(Type $type): bool
    {
        if ($type->getBuiltinType() !== Type::BUILTIN_TYPE_OBJECT) {
            return false;
        }

        $className = $type->getClassName();
        if ($className === null || $className === '' || $className === '0') {
            return false;
        }

        return $className === Generator::class || is_subclass_of($className, Generator::class);
    }

    public static function isIteratorType(Type $type): bool
    {
        if ($type->getBuiltinType() !== Type::BUILTIN_TYPE_OBJECT) {
            return false;
        }

        $className = $type->getClassName();
        if ($className === null || $className === '' || $className === '0') {
            return false;
        }

        return $className === Iterator::class || is_subclass_of($className, Iterator::class);
    }

    public static function isArrayType(Type $type): bool
    {
        return $type->getBuiltinType() === Type::BUILTIN_TYPE_ARRAY;
    }

    public static function isIterableType(Type $type): bool
    {
        return $type->getBuiltinType() === Type::BUILTIN_TYPE_ITERABLE;
    }

    public static function isStringType(Type $type): bool
    {
        return $type->getBuiltinType() === Type::BUILTIN_TYPE_STRING;
    }

    public static function isIntType(Type $type): bool
    {
        return $type->getBuiltinType() === Type::BUILTIN_TYPE_INT;
    }

    public static function isFloatType(Type $type): bool
    {
        return $type->getBuiltinType() === Type::BUILTIN_TYPE_FLOAT;
    }

    public static function isBooleanType(Type $type): bool
    {
        return in_array(
            $type->getBuiltinType(), [
            Type::BUILTIN_TYPE_BOOL,
            Type::BUILTIN_TYPE_TRUE,
            Type::BUILTIN_TYPE_FALSE,
            ], true
        );
    }

    public static function isResourceType(Type $type): bool
    {
        return $type->getBuiltinType() === Type::BUILTIN_TYPE_RESOURCE;
    }

    public static function isSubtype(Type $type, Type $subtype): bool
    {
        if (!$type->isNullable() && $subtype->isNullable()) {
            return false;
        }

        if ($type->isCollection() !== $subtype->isCollection()) {
            return false;
        }

        if (self::isResourceType($type)) {
            return self::isResourceType($subtype);
        }

        if (self::isArrayType($type)) {
            if (!self::isArrayType($subtype) && !self::isArrayAccessType($subtype)) {
                return false;
            }

            $typeCollectionKeyTypes = $type->getCollectionKeyTypes();
            $subtypeCollectionKeyTypes = $subtype->getCollectionKeyTypes();
            if (count($typeCollectionKeyTypes) !== count($subtypeCollectionKeyTypes)) {
                return false;
            }

            sort($typeCollectionKeyTypes);
            sort($subtypeCollectionKeyTypes);

            foreach ($typeCollectionKeyTypes as $k => $typeCollectionKeyType) {
                if (!self::isSubtype($typeCollectionKeyType, $subtypeCollectionKeyTypes[$k])) {
                    return false;
                }
            }

            $typeCollectionValueTypes = $type->getCollectionValueTypes();
            $subtypeCollectionValueTypes = $subtype->getCollectionValueTypes();

            if (count($typeCollectionValueTypes) !== count($subtypeCollectionValueTypes)) {
                return false;
            }

            sort($typeCollectionValueTypes);
            sort($subtypeCollectionValueTypes);

            foreach ($typeCollectionValueTypes as $k => $typeCollectionValueType) {
                if (!self::isSubtype($typeCollectionValueType, $subtypeCollectionValueTypes[$k])) {
                    return false;
                }
            }

            return true;
        }

        if (self::isEnumType($type)) {
            if (!self::isEnumType($subtype)) {
                return false;
            }

            $typeClassName = $type->getClassName();
            $subtypeClassName = $subtype->getClassName();

            return $typeClassName !== null && $typeClassName === $subtypeClassName;
        }

        if (self::isGeneratorType($type)) {
            if (!self::isGeneratorType($subtype)) {
                return false;
            }

            /**
 * @var class-string|null $typeClassName 
*/
            $typeClassName = $type->getClassName();
            $subtypeClassName = $subtype->getClassName();

            $subclassOrSameClass = $typeClassName !== null && $subtypeClassName !== null
                && ($typeClassName === $subtypeClassName || is_subclass_of($subtypeClassName, $typeClassName));

            if (!$subclassOrSameClass) {
                return false;
            }

            $typeCollectionKeyTypes = $type->getCollectionKeyTypes();
            $subtypeCollectionKeyTypes = $subtype->getCollectionKeyTypes();
            if (count($typeCollectionKeyTypes) !== count($subtypeCollectionKeyTypes)) {
                return false;
            }

            sort($typeCollectionKeyTypes);
            sort($subtypeCollectionKeyTypes);

            foreach ($typeCollectionKeyTypes as $k => $typeCollectionKeyType) {
                if (!self::isSubtype($typeCollectionKeyType, $subtypeCollectionKeyTypes[$k])) {
                    return false;
                }
            }

            $typeCollectionValueTypes = $type->getCollectionValueTypes();
            $subtypeCollectionValueTypes = $subtype->getCollectionValueTypes();

            if (count($typeCollectionValueTypes) !== count($subtypeCollectionValueTypes)) {
                return false;
            }

            sort($typeCollectionValueTypes);
            sort($subtypeCollectionValueTypes);

            foreach ($typeCollectionValueTypes as $k => $typeCollectionValueType) {
                if (!self::isSubtype($typeCollectionValueType, $subtypeCollectionValueTypes[$k])) {
                    return false;
                }
            }

            return true;
        }

        if (self::isIteratorAggregateType($type)) {
            if (!self::isIteratorAggregateType($subtype)) {
                return false;
            }

            /**
 * @var class-string|null $typeClassName 
*/
            $typeClassName = $type->getClassName();
            $subtypeClassName = $subtype->getClassName();

            $subclassOrSameClass = $typeClassName !== null && $subtypeClassName !== null
                && ($typeClassName === $subtypeClassName || is_subclass_of($subtypeClassName, $typeClassName));

            if (!$subclassOrSameClass) {
                return false;
            }

            $typeCollectionKeyTypes = $type->getCollectionKeyTypes();
            $subtypeCollectionKeyTypes = $subtype->getCollectionKeyTypes();
            if (count($typeCollectionKeyTypes) !== count($subtypeCollectionKeyTypes)) {
                return false;
            }

            sort($typeCollectionKeyTypes);
            sort($subtypeCollectionKeyTypes);

            foreach ($typeCollectionKeyTypes as $k => $typeCollectionKeyType) {
                if (!self::isSubtype($typeCollectionKeyType, $subtypeCollectionKeyTypes[$k])) {
                    return false;
                }
            }

            $typeCollectionValueTypes = $type->getCollectionValueTypes();
            $subtypeCollectionValueTypes = $subtype->getCollectionValueTypes();

            if (count($typeCollectionValueTypes) !== count($subtypeCollectionValueTypes)) {
                return false;
            }

            sort($typeCollectionValueTypes);
            sort($subtypeCollectionValueTypes);

            foreach ($typeCollectionValueTypes as $k => $typeCollectionValueType) {
                if (!self::isSubtype($typeCollectionValueType, $subtypeCollectionValueTypes[$k])) {
                    return false;
                }
            }

            return true;
        }

        if (self::isIteratorType($type)) {
            if (!self::isIteratorType($subtype) && !self::isGeneratorType($subtype)) {
                return false;
            }

            /**
 * @var class-string|null $typeClassName 
*/
            $typeClassName = $type->getClassName();
            $subtypeClassName = $subtype->getClassName();

            $subclassOrSameClass = $typeClassName !== null && $subtypeClassName !== null
                && ($typeClassName === $subtypeClassName || is_subclass_of($subtypeClassName, $typeClassName));

            if (!$subclassOrSameClass) {
                return false;
            }

            $typeCollectionKeyTypes = $type->getCollectionKeyTypes();
            $subtypeCollectionKeyTypes = $subtype->getCollectionKeyTypes();
            if (count($typeCollectionKeyTypes) !== count($subtypeCollectionKeyTypes)) {
                return false;
            }

            sort($typeCollectionKeyTypes);
            sort($subtypeCollectionKeyTypes);

            foreach ($typeCollectionKeyTypes as $k => $typeCollectionKeyType) {
                if (!self::isSubtype($typeCollectionKeyType, $subtypeCollectionKeyTypes[$k])) {
                    return false;
                }
            }

            $typeCollectionValueTypes = $type->getCollectionValueTypes();
            $subtypeCollectionValueTypes = $subtype->getCollectionValueTypes();

            if (count($typeCollectionValueTypes) !== count($subtypeCollectionValueTypes)) {
                return false;
            }

            sort($typeCollectionValueTypes);
            sort($subtypeCollectionValueTypes);

            foreach ($typeCollectionValueTypes as $k => $typeCollectionValueType) {
                if (!self::isSubtype($typeCollectionValueType, $subtypeCollectionValueTypes[$k])) {
                    return false;
                }
            }

            return true;
        }

        if (self::isIterableType($type)) {
            if (!(self::isIterableType($subtype)
                || self::isIteratorType($subtype)
                || self::isArrayType($subtype)
                || self::isIteratorAggregateType($subtype)
                || self::isExactlyTraversableType($subtype))
            ) {
                return false;
            }

            $typeCollectionKeyTypes = $type->getCollectionKeyTypes();
            $subtypeCollectionKeyTypes = $subtype->getCollectionKeyTypes();
            if (count($typeCollectionKeyTypes) !== count($subtypeCollectionKeyTypes)) {
                return false;
            }

            sort($typeCollectionKeyTypes);
            sort($subtypeCollectionKeyTypes);

            foreach ($typeCollectionKeyTypes as $k => $typeCollectionKeyType) {
                if (!self::isSubtype($typeCollectionKeyType, $subtypeCollectionKeyTypes[$k])) {
                    return false;
                }
            }

            $typeCollectionValueTypes = $type->getCollectionValueTypes();
            $subtypeCollectionValueTypes = $subtype->getCollectionValueTypes();

            if (count($typeCollectionValueTypes) !== count($subtypeCollectionValueTypes)) {
                return false;
            }

            sort($typeCollectionValueTypes);
            sort($subtypeCollectionValueTypes);

            foreach ($typeCollectionValueTypes as $k => $typeCollectionValueType) {
                if (!self::isSubtype($typeCollectionValueType, $subtypeCollectionValueTypes[$k])) {
                    return false;
                }
            }

            return true;
        }

        if (self::isExactlyTraversableType($type)) {
            if (!(self::isExactlyTraversableType($subtype)
                || self::isIteratorType($subtype)
                || self::isIteratorAggregateType($subtype)                )
            ) {
                return false;
            }

            $typeCollectionKeyTypes = $type->getCollectionKeyTypes();
            $subtypeCollectionKeyTypes = $subtype->getCollectionKeyTypes();
            if (count($typeCollectionKeyTypes) !== count($subtypeCollectionKeyTypes)) {
                return false;
            }

            sort($typeCollectionKeyTypes);
            sort($subtypeCollectionKeyTypes);

            foreach ($typeCollectionKeyTypes as $k => $typeCollectionKeyType) {
                if (!self::isSubtype($typeCollectionKeyType, $subtypeCollectionKeyTypes[$k])) {
                    return false;
                }
            }

            $typeCollectionValueTypes = $type->getCollectionValueTypes();
            $subtypeCollectionValueTypes = $subtype->getCollectionValueTypes();

            if (count($typeCollectionValueTypes) !== count($subtypeCollectionValueTypes)) {
                return false;
            }

            sort($typeCollectionValueTypes);
            sort($subtypeCollectionValueTypes);

            foreach ($typeCollectionValueTypes as $k => $typeCollectionValueType) {
                if (!self::isSubtype($typeCollectionValueType, $subtypeCollectionValueTypes[$k])) {
                    return false;
                }
            }

            return true;
        }

        if (self::isObjectType($type)) {
            if (!self::isObjectType($subtype)) {
                return false;
            }

            /**
 * @var class-string|null $typeClassName 
*/
            $typeClassName = $type->getClassName();
            $subtypeClassName = $subtype->getClassName();

            return $typeClassName !== null && $subtypeClassName !== null
                && ($typeClassName === $subtypeClassName || is_subclass_of($subtypeClassName, $typeClassName));
        }

        if (self::isIntType($type)) {
            return self::isIntType($subtype);
        }

        if (self::isFloatType($type)) {
            if (self::isFloatType($subtype)) {
                return true;
            }

            return self::isIntType($subtype);
        }

        if (self::isBooleanType($type)) {
            return in_array(
                $subtype->getBuiltinType(),
                [Type::BUILTIN_TYPE_BOOL, Type::BUILTIN_TYPE_FALSE, Type::BUILTIN_TYPE_TRUE],
                true
            );
        }


        return true;
    }

    public static function equals(Type $one, Type $two): bool
    {
        if ($one->getBuiltinType() !== $two->getBuiltinType()) {
            return false;
        }

        if ($one->getClassName() !== $two->getClassName()) {
            return false;
        }

        if ($one->isCollection() !== $two->isCollection()) {
            return false;
        }

        if ($one->isNullable() !== $two->isNullable()) {
            return false;
        }

        $oneCollectionKeyTypes = $one->getCollectionKeyTypes();
        $twoCollectionKeyTypes = $two->getCollectionKeyTypes();

        if (\count($oneCollectionKeyTypes) !== \count($twoCollectionKeyTypes)) {
            return false;
        }

        sort($oneCollectionKeyTypes);
        sort($twoCollectionKeyTypes);

        foreach ($oneCollectionKeyTypes as $k => $oneCollectionKeyType) {
            if (!self::equals($oneCollectionKeyType, $twoCollectionKeyTypes[$k])) {
                return false;
            }
        }

        $oneCollectionValueTypes = $one->getCollectionValueTypes();
        $twoCollectionValueTypes = $two->getCollectionValueTypes();

        if (\count($oneCollectionValueTypes) !== \count($twoCollectionValueTypes)) {
            return false;
        }

        sort($oneCollectionValueTypes);
        sort($twoCollectionValueTypes);

        foreach ($oneCollectionValueTypes as $k => $oneCollectionValueType) {
            if (!self::equals($oneCollectionValueType, $twoCollectionValueTypes[$k])) {
                return false;
            }
        }

        return true;
    }

    public static function isScalarType(Type $type): bool
    {
        return in_array(
            $type->getBuiltinType(), [
            Type::BUILTIN_TYPE_BOOL,
            Type::BUILTIN_TYPE_TRUE,
            Type::BUILTIN_TYPE_FALSE,
            Type::BUILTIN_TYPE_STRING,
            Type::BUILTIN_TYPE_INT,
            Type::BUILTIN_TYPE_FLOAT,
            Type::BUILTIN_TYPE_RESOURCE,
            ], true
        );
    }

}
