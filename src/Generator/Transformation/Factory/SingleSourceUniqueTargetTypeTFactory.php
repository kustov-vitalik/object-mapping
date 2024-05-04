<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory;

use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformationFactory;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;

abstract class SingleSourceUniqueTargetTypeTFactory implements ITransformationFactory
{
    public function getTransformation(
        array $sourceTypeInfos,
        array $targetTypeInfo,
        TFactoryCtx $tFactoryCtx
    ): ITransformation {
        $sourceTypeInfoCount = count($sourceTypeInfos);
        if ($sourceTypeInfoCount === 0) {
            throw new NotImplementedYet(__METHOD__);
        }

        if ($sourceTypeInfoCount > 1) {
            throw new NoTransformationException(__METHOD__);
        }

        $sourceTypeInfo = $sourceTypeInfos[0];

        $targetTypeInfoCount = count($targetTypeInfo);
        if ($targetTypeInfoCount === 0) {
            throw new NotImplementedYet(__METHOD__);
        }

        if ($targetTypeInfoCount > 1) {
            throw new NoTransformationException(__METHOD__);
        }

        $targetType = $targetTypeInfo[0];

        return $this->createTransformation($sourceTypeInfo, $targetType, $tFactoryCtx);
    }

    /**
     * @param Type[] $sourceTypeInfo
     */
    abstract public function createTransformation(
        array $sourceTypeInfo,
        Type $targetType,
        TFactoryCtx $tFactoryCtx
    ): ITransformation;

}
