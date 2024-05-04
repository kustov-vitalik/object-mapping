<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation;

use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\NoTransformationException;

interface ITransformationFactory
{
    /**
     * @param  array<int, Type[]> $sourceTypeInfos
     * @param  Type[]             $targetTypeInfo
     * @throws NoTransformationException
     */
    public function getTransformation(
        array $sourceTypeInfos,
        array $targetTypeInfo,
        TFactoryCtx $tFactoryCtx
    ): ITransformation;
}
