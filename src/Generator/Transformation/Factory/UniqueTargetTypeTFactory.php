<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory;

use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformationFactory;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;

abstract class UniqueTargetTypeTFactory implements ITransformationFactory
{
    public function __construct(
        protected CompositeTFactory $compositeTFactory,
    ) {
    }

    public function getTransformation(
        array $sourceTypeInfos,
        array $targetTypeInfo,
        TFactoryCtx $tFactoryCtx
    ): ITransformation {
        $targetTypeInfoCount = count($targetTypeInfo);
        if ($targetTypeInfoCount === 0) {
            throw new NotImplementedYet();
        }

        if ($targetTypeInfoCount > 1) {
            throw new NoTransformationException();
        }

        $targetType = $targetTypeInfo[0];

        return $this->createTransformation($sourceTypeInfos, $targetType, $tFactoryCtx);
    }

    /**
     * @throws NoTransformationException
     */
    abstract public function createTransformation(
        array $sourceTypeInfos,
        Type $targetType,
        TFactoryCtx $tFactoryCtx
    ): ITransformation;
}
