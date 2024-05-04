<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource;

use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Exception\UnSafeOperationException;
use VKPHPUtils\Mapping\Generator\Transformation\EmptyTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\SingleSourceUniqueTargetTypeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Generator\Transformation\ToString\FromBooleanTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToString\FromFloatTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToString\FromIntTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToString\FromObjectTransformation;
use VKPHPUtils\Mapping\Helper\TypeChecker;

class ToStringTFactory extends SingleSourceUniqueTargetTypeTFactory
{

    public function createTransformation(array $sourceTypeInfo, Type $targetType, TFactoryCtx $tFactoryCtx): ITransformation
    {
        if (!TypeChecker::isStringType($targetType)) {
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

        return match (true) {
            TypeChecker::isStringType($sourceType) => new EmptyTransformation(),
            TypeChecker::isIntType($sourceType) => new FromIntTransformation(),
            TypeChecker::isFloatType($sourceType) => new FromFloatTransformation(),
            TypeChecker::isBooleanType($sourceType) => new FromBooleanTransformation(),
            TypeChecker::isObjectType($sourceType) => new FromObjectTransformation($sourceType->getClassName(), $tFactoryCtx),
            default => throw new UnSafeOperationException($sourceType, $targetType)
        };
    }
}
