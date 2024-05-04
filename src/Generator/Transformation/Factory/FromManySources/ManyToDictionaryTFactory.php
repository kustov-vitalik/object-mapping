<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory\FromManySources;

use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\CompositeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\UniqueTargetTypeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\TypeChecker;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;

class ManyToDictionaryTFactory extends UniqueTargetTypeTFactory
{
    public function __construct(
        protected CompositeTFactory $compositeTFactory,
        private readonly ReflectionMethod $reflectionMethod,
    ) {
        parent::__construct($this->compositeTFactory);
    }

    public function createTransformation(array $sourceTypeInfos, Type $targetType, TFactoryCtx $tFactoryCtx): ITransformation
    {
        if (!TypeChecker::isArrayType($targetType)) {
            throw new NoTransformationException();
        }

        if (!$this->reflectionMethod->hasToDictionary()) {
            throw new NoTransformationException();
        }

        throw new NotImplementedYet();
    }
}
