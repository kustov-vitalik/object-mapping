<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource;

use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\CompositeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\SingleSourceUniqueTargetTypeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Generator\Transformation\ToObject\FromAnyTypeTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToObject\FromArrayAccessObjectTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToObject\FromArrayTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToObject\FromObjectTransformation;
use VKPHPUtils\Mapping\Helper\TypeChecker;

class ToObjectTFactory extends SingleSourceUniqueTargetTypeTFactory
{

    public function __construct(
        private readonly CompositeTFactory $compositeTFactory,
    ) {
    }

    public function createTransformation(array $sourceTypeInfo, Type $targetType, TFactoryCtx $tFactoryCtx): ITransformation
    {
        if (!TypeChecker::isObjectType($targetType)) {
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
        $targetClassName = $targetType->getClassName();
        if ($targetClassName === null) {
            throw new NotImplementedYet('for empty target class');
        }

        return match (true) {
            TypeChecker::isArrayType($sourceType) => new FromArrayTransformation(
                $targetClassName, $this->compositeTFactory, $tFactoryCtx
            ),
            TypeChecker::isArrayAccessType($sourceType) => new FromArrayAccessObjectTransformation(
                $targetClassName, $this->compositeTFactory, $tFactoryCtx
            ),
            TypeChecker::isObjectType($sourceType) => new FromObjectTransformation(
                $targetClassName, $this->compositeTFactory, $tFactoryCtx
            ),
            default => new FromAnyTypeTransformation($targetClassName, $this->compositeTFactory, $tFactoryCtx)
        };
    }
}
