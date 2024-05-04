<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToObject;

use VKPHPUtils\Mapping\Generator\Extractor\Reader;
use VKPHPUtils\Mapping\Generator\Extractor\Writer;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Extractor\Extractor;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\CompositeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;

class FromArrayAccessObjectTransformation extends AbstractToObjectTransformation
{

    public function __construct(
        private readonly string $targetClassName,
        private readonly CompositeTFactory $compositeTFactory,
        private readonly TFactoryCtx $tFactoryCtx,
    ) {
    }

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        $reflectionClass = ReflectionClass::forName($this->targetClassName);
        $prototypeMethod = $this->tFactoryCtx->reflectionMethod;
        $propertiesToSet = $this->getPropertiesToSet($prototypeMethod, $reflectionClass, $methodVarScope);

        $builderFactory = new BuilderFactory();
        $extractor = new Extractor();

        // read sources. put in property vars
        $sourceParameter = $this->tFactoryCtx->map->values()[0];
        $sourceParameterVar = $methodVarScope->getParameterVar($sourceParameter);
        foreach ($propertiesToSet as $propertyName => $property) {
            $mapping = $prototypeMethod->getMapping($propertyName);
            $sourcePropertyName = $mapping->source;
            $sourceReader = $extractor->getReader('array', $sourcePropertyName);
            if (!$sourceReader instanceof Reader) {
                continue;
            }


            // todo double check source type
            $sourcePropertyTypeInfo = $property->getTypeInfo();

            $transformation = $this->compositeTFactory->getTransformation(
                [$sourcePropertyTypeInfo],
                $property->getTypeInfo(),
                new TFactoryCtx(
                    new Map(),
                    $prototypeMethod
                )
            );

            $propertyVar = $methodVarScope->getTargetPropertyVar($property);
            $method->addStmt(
                $builderFactory->assign(
                    $propertyVar,
                    $transformation->transform(
                        $sourceReader->getExpr($sourceParameterVar),
                        $methodVarScope,
                        $method,
                        $classBuilder
                    )
                )
            );
        }

        // construct object
        if ($this->constructObjectIfNeededCheckShouldReturn(
            $prototypeMethod,
            $reflectionClass,
            $methodVarScope,
            $propertiesToSet,
            $method
        )
        ) {
            return $methodVarScope->getTargetVar();
        }

        // set remaining props
        foreach ($propertiesToSet as $propertyName => $property) {
            $mapping = $prototypeMethod->getMapping($propertyName);
            $propertyWriter = $extractor->getWriter($this->targetClassName, $mapping->target);

            if (!$propertyWriter instanceof Writer) {
                // todo double check
                continue;
            }

            $method->addStmt(
                $propertyWriter->getExpr(
                    $methodVarScope->getTargetVar(),
                    $methodVarScope->getTargetPropertyVar($property)
                )
            );
            $propertiesToSet->remove($propertyName);
        }

        if (!$propertiesToSet->isEmpty()) {
            throw new InvalidConfigException(
                "Invalid config. Not all properties were set. Remaining properties: " . implode(
                    ', ',
                    $propertiesToSet->keys()
                )
            );
        }

        return $methodVarScope->getTargetVar();
    }
}
