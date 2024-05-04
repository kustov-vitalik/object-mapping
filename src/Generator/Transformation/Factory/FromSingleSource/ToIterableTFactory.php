<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource;

use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\SingleSourceUniqueTargetTypeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Generator\Transformation\ToCollection\ToIterable\FromArrayTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToCollection\ToIterable\FromGeneratorTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToCollection\ToIterable\FromIterableTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToCollection\ToIterable\FromIteratorAggregateTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToCollection\ToIterable\FromIteratorTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToCollection\ToIterable\FromTraversableTransformation;
use VKPHPUtils\Mapping\Helper\TypeChecker;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;

class ToIterableTFactory extends SingleSourceUniqueTargetTypeTFactory
{
    public function __construct(
        private readonly ReflectionMethod $reflectionMethod,
    ) {
    }

    public function createTransformation(array $sourceTypeInfo, Type $targetType, TFactoryCtx $tFactoryCtx): ITransformation
    {
        if (!TypeChecker::isIterableType($targetType)) {
            throw new NoTransformationException(__METHOD__);
        }

        if (!$this->reflectionMethod->hasToCollection()) {
            throw new InvalidConfigException('Forgot #[ToCollection] annotation?');
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
            TypeChecker::isArrayType($sourceType) => new FromArrayTransformation($tFactoryCtx),
            TypeChecker::isGeneratorType($sourceType) => new FromGeneratorTransformation($tFactoryCtx),
            TypeChecker::isIteratorAggregateType($sourceType) => new FromIteratorAggregateTransformation($tFactoryCtx),
            TypeChecker::isIteratorType($sourceType) => new FromIteratorTransformation($tFactoryCtx),
            TypeChecker::isExactlyTraversableType($sourceType) => new FromTraversableTransformation($tFactoryCtx),
            TypeChecker::isIterableType($sourceType) => new FromIterableTransformation($tFactoryCtx),
            default => throw new NotImplementedYet(__METHOD__ . ' ::: ' . print_r($sourceType, true))
        };
    }
}
