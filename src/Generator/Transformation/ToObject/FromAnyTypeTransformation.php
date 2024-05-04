<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToObject;

use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Generator\Extractor\Writer;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Extractor\Extractor;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\CompositeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;

class FromAnyTypeTransformation extends AbstractToObjectTransformation
{
    /**
     * @param class-string $targetClassName
     */
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

        $parameters = $this->tFactoryCtx->map;
        $extractor = new Extractor();
        $builderFactory = new BuilderFactory();

        foreach ($propertiesToSet as $propertyName => $property) {
            $mapping = $prototypeMethod->getMapping($propertyName);

            if ($mapping->qualifier instanceof Qualifier) {
                // mapper logic
                throw new NotImplementedYet(__METHOD__);
            }

            foreach ($parameters as $sourceParameterName => $sourceParameter) {
                if ($mapping->source === $sourceParameterName) {
                    $ctxParameters = new Map();
                    $ctxParameters->put($sourceParameterName, $sourceParameter);
                    $transformation = $this->compositeTFactory->getTransformation(
                        [$sourceParameter->getTypeInfo()],
                        $property->getTypeInfo(),
                        new TFactoryCtx(
                            $ctxParameters,
                            $prototypeMethod
                        ),
                    );

                    $method->addStmt(
                        $builderFactory->assign(
                            $methodVarScope->getTargetPropertyVar($property),
                            $transformation->transform($expr, $methodVarScope, $method, $classBuilder)
                        )
                    );
                }
            }
        }

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

        foreach ($propertiesToSet as $propertyName => $property) {
            $mapping = $prototypeMethod->getMapping($propertyName);
            $propertyWriter = $extractor->getWriter($this->targetClassName, $mapping->target);
            if (!$propertyWriter instanceof Writer) {
                throw new NotImplementedYet(__METHOD__);
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
            throw new InvalidConfigException("Properties to set: " . implode(', ', $propertiesToSet->keys()));
        }

        return $methodVarScope->getTargetVar();
    }
}
