<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToDictionary;

use VKPHPUtils\Mapping\Generator\Extractor\Reader;
use VKPHPUtils\Mapping\Generator\Extractor\Writer;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\DS\TargetPropertiesHeap;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Extractor\Extractor;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\CompositeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

class FromObjectTransformation implements ITransformation
{
    /**
     * @param class-string $sourceClassName
     */
    public function __construct(
        private readonly CompositeTFactory $compositeTFactory,
        private readonly ReflectionMethod $reflectionMethod,
        private readonly TFactoryCtx $tFactoryCtx,
        private readonly string $sourceClassName,
    ) {
    }

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        $reflectionClass = ReflectionClass::forName($this->sourceClassName);

        $map = new Map();
        foreach (new TargetPropertiesHeap($this->reflectionMethod, ...$reflectionClass->getMapperProperties()) as $property) {
            $mapping = $this->reflectionMethod->getMapping($property->name);
            if ($mapping->ignore) {
                continue;
            }

            $map->put($property->name, $property);
            $methodVarScope->putTargetProperty($property);
        }


        $builderFactory = new BuilderFactory();
        $extractor = new Extractor();
        $readStmts = [];
        foreach ($map as $propertyName => $property) {
            $propertyVar = $methodVarScope->getTargetPropertyVar($property);
            $mapping = $this->reflectionMethod->findMappingBySource($propertyName);
            $sourcePropertyName = $mapping?->source ?? $propertyName;
            $targetPropertyName = $mapping?->target ?? $propertyName;
            $sourceReader = $extractor->getReader($this->sourceClassName, $sourcePropertyName);

            $sourceTypeInfo = $property->getTypeInfo();

            // todo double check
            $targetTypeInfo = $sourceTypeInfo;

            $transformation = $this->compositeTFactory->getTransformation(
                [$sourceTypeInfo],
                $targetTypeInfo,
                new TFactoryCtx($this->tFactoryCtx->map, $this->tFactoryCtx->reflectionMethod)
            );

            if ($sourceReader instanceof Reader) {
                $readStmts[] = $builderFactory->assign(
                    $propertyVar,
                    $transformation->transform(
                        $sourceReader->getExpr($expr),
                        $methodVarScope,
                        $method,
                        $classBuilder
                    )
                );
            } else {
                // todo double check
            }
        }

        $method->addStmts($readStmts);


        // create object if needed
        if (!$this->reflectionMethod->hasTargetParameter()) {
            $method->addStmt($builderFactory->assign($methodVarScope->getTargetVar(), $builderFactory->array()));
        }

        // fill properties
        $assignStmts = [];
        foreach ($map as $propertyName => $property) {
            $propertyVar = $methodVarScope->getTargetPropertyVar($property);
            $mapping = $this->reflectionMethod->findMappingBySource($propertyName);
            $targetPropertyName = $mapping?->target ?? $propertyName;
            $targetWriter = $extractor->getWriter('array', $targetPropertyName);


            if ($targetWriter instanceof Writer) {
                $assignStmts[] = $targetWriter->getExpr(
                    $methodVarScope->getTargetVar(),
                    $propertyVar
                );

                $map->remove($property->name);
            } else {
                // todo double check
            }
        }

        if (!$map->isEmpty()) {
            throw new NotImplementedYet(__METHOD__ . '  invalid map config');
        }

        $method->addStmts($assignStmts);


        return $methodVarScope->getTargetVar();
    }
}
