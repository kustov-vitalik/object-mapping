<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource;

use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Attributes\MapEnumStrategy;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Exception\RuntimeException;
use VKPHPUtils\Mapping\Generator\Transformation\EmptyTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\SingleSourceUniqueTargetTypeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromEnumToEnumByNameTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromEnumToIntBackedEnumTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromEnumToStringBackedEnumTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromIntBackedEnumToEnumByNameTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromIntBackedEnumToIntBackedEnumTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromIntBackedEnumToStringBackedEnumTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromIntToBackedEnumByValueTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromStringBackedEnumToEnumByNameTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromStringBackedEnumToIntBackedEnumByNameTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromStringBackedEnumToStringBackedEnumByNameTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromStringToBackedEnumByNameTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToEnum\FromStringToEnumByNameTransformation;
use VKPHPUtils\Mapping\Helper\TypeChecker;

class ToEnumTFactory extends SingleSourceUniqueTargetTypeTFactory
{

    public function __construct(
        private readonly MapEnumStrategy $mapEnumStrategy,
    ) {
    }

    public function createTransformation(array $sourceTypeInfo, Type $targetType, TFactoryCtx $tFactoryCtx): ITransformation
    {
        if (!TypeChecker::isEnumType($targetType)) {
            throw new NoTransformationException(__METHOD__);
        }

        $sourceTypeInfoCount = count($sourceTypeInfo);
        if ($sourceTypeInfoCount === 0) {
            throw new NotImplementedYet(__METHOD__);
        }

        if ($sourceTypeInfoCount > 1) {
            throw new NotImplementedYet(__METHOD__);
        }

        $sourceType = $sourceTypeInfo[0];

        if (TypeChecker::equals($targetType, $sourceType)) {
            return new EmptyTransformation();
        }

        $targetClassName = $targetType->getClassName();
        if ($targetClassName === null) {
            throw new RuntimeException('Enum with no class name. Current type: ' . print_r($targetType, true));
        }

        switch ($this->mapEnumStrategy) {
        case MapEnumStrategy::TRY_BY_VALUE_THEN_BY_NAME:
        case MapEnumStrategy::TRY_BY_VALUE:
        case MapEnumStrategy::TRY_BY_NAME_THEN_BY_VALUE:
        case MapEnumStrategy::AUTO:
        case MapEnumStrategy::TRY_BY_NAME:
            break;
        default:
            throw new NotImplementedYet(__METHOD__);
        }


        if (TypeChecker::isIntBackedEnumType($targetType)) {
            return match (true) {
                TypeChecker::isStringBackedEnumType(
                    $sourceType
                ) => new FromStringBackedEnumToIntBackedEnumByNameTransformation(
                    $sourceType->getClassName(), $targetClassName
                ),
                TypeChecker::isIntBackedEnumType($sourceType) => new FromIntBackedEnumToIntBackedEnumTransformation(
                    $sourceType->getClassName(), $targetClassName
                ),
                TypeChecker::isEnumType($sourceType) => new FromEnumToIntBackedEnumTransformation(
                    $sourceType->getClassName(), $targetClassName
                ),
                TypeChecker::isIntType($sourceType) => new FromIntToBackedEnumByValueTransformation($targetClassName),
                TypeChecker::isStringType($sourceType) => new FromStringToBackedEnumByNameTransformation(
                    $targetClassName
                ),
                default => throw new RuntimeException('invalid type: ' . print_r($sourceType, true))
            };
        }

        if (TypeChecker::isStringBackedEnumType($targetType)) {
            return match (true) {
                TypeChecker::isStringBackedEnumType(
                    $sourceType
                ) => new FromStringBackedEnumToStringBackedEnumByNameTransformation(
                    $sourceType->getClassName(),
                    $targetClassName
                ),
                TypeChecker::isIntBackedEnumType($sourceType) => new FromIntBackedEnumToStringBackedEnumTransformation(
                    $sourceType->getClassName(),
                    $targetClassName
                ),
                TypeChecker::isEnumType($sourceType) => new FromEnumToStringBackedEnumTransformation(
                    $sourceType->getClassName(),
                    $targetClassName
                ),
                TypeChecker::isStringType($sourceType) => new FromStringToBackedEnumByNameTransformation(
                    $targetClassName
                ),
                default => throw new RuntimeException('invalid type: ' . print_r($sourceType, true))
            };
        }

        return match (true) {
            TypeChecker::isStringBackedEnumType(
                $sourceType
            ) => new FromStringBackedEnumToEnumByNameTransformation(
                $sourceType->getClassName(), $targetClassName
            ),
            TypeChecker::isIntBackedEnumType($sourceType) => new FromIntBackedEnumToEnumByNameTransformation(
                $sourceType->getClassName(), $targetClassName
            ),
            TypeChecker::isEnumType($sourceType) => new FromEnumToEnumByNameTransformation(
                $sourceType->getClassName(), $targetClassName
            ),
            TypeChecker::isStringType($sourceType) => new FromStringToEnumByNameTransformation($targetClassName),
            default => throw new RuntimeException('invalid type: ' . print_r($sourceType, true))
        };
    }
}
