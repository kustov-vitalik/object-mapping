<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory\FromManySources;

use LogicException;
use stdClass;
use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\UniqueTargetTypeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Generator\Transformation\ToObject\FromManySourcesObjectTransformation;
use VKPHPUtils\Mapping\Helper\TypeChecker;

class ManyToObjectTFactory extends UniqueTargetTypeTFactory
{

    public function createTransformation(array $sourceTypeInfos, Type $targetType, TFactoryCtx $tFactoryCtx): ITransformation
    {
        if (!TypeChecker::isObjectType($targetType)) {
            throw new NoTransformationException(__METHOD__);
        }

        $sourceTypeInfoCount = count($sourceTypeInfos);
        if ($sourceTypeInfoCount === 0) {
            throw new NotImplementedYet(__METHOD__);
        }

        if ($sourceTypeInfoCount === 1) {
            throw new LogicException("The factory should not be called");
        }

        $targetClassName = $targetType->getClassName() ?? stdClass::class;

        return new FromManySourcesObjectTransformation($targetClassName, $this->compositeTFactory, $tFactoryCtx);
    }
}
