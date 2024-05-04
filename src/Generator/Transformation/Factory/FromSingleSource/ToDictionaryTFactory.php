<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource;

use stdClass;
use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\CompositeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\SingleSourceUniqueTargetTypeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Generator\Transformation\ToDictionary\FromArrayAccessObjectTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToDictionary\FromArrayTransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ToDictionary\FromObjectTransformation;
use VKPHPUtils\Mapping\Helper\TypeChecker;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;

class ToDictionaryTFactory extends SingleSourceUniqueTargetTypeTFactory
{
    public function __construct(
        private readonly CompositeTFactory $compositeTFactory,
        private readonly ReflectionMethod $reflectionMethod,
    ) {
    }

    public function createTransformation(array $sourceTypeInfo, Type $targetType, TFactoryCtx $tFactoryCtx): ITransformation
    {
        if (!TypeChecker::isArrayType($targetType)) {
            throw new NoTransformationException(__METHOD__);
        }

        if (!$this->reflectionMethod->hasToDictionary()) {
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

        /**
 * @var class-string $sourceTypeClassName 
*/
        $sourceTypeClassName = $sourceType->getClassName() ?? stdClass::class;

        return match (true) {
            TypeChecker::isArrayType($sourceType) => new FromArrayTransformation($this->reflectionMethod),
            TypeChecker::isArrayAccessType($sourceType) => new FromArrayAccessObjectTransformation($this->reflectionMethod),
            TypeChecker::isObjectType($sourceType) => new FromObjectTransformation(
                $this->compositeTFactory,
                $this->reflectionMethod,
                $tFactoryCtx,
                $sourceTypeClassName
            ),
            default => throw new NotImplementedYet(__METHOD__ . ' ::: ' . print_r($sourceType, true))
        };
    }
}
