<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\Factory;

use VKPHPUtils\Mapping\Exception\NoTransformationException;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformationFactory;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;

class CompositeTFactory implements ITransformationFactory
{
    /**
     * @var ITransformationFactory[] 
     */
    private array $factories;

    public function __construct(ITransformationFactory...$transformationFactory)
    {
        $this->factories = $transformationFactory;
    }

    public function getTransformation(
        array $sourceTypeInfos,
        array $targetTypeInfo,
        TFactoryCtx $tFactoryCtx
    ): ITransformation {
        foreach ($this->factories as $factory) {
            try {
                return $factory->getTransformation($sourceTypeInfos, $targetTypeInfo, $tFactoryCtx);
            } catch (NoTransformationException) {
            }
        }

        throw new NoTransformationException(
            'From: ' . print_r($sourceTypeInfos, true) . ' :::: To: ' . print_r($targetTypeInfo, true)
        );
    }

    public function addTransformationFactory(ITransformationFactory $transformationFactory): void
    {
        $this->factories[] = $transformationFactory;
    }
}
