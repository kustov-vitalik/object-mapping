<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToObject;

use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Generator\Extractor\Reader;
use VKPHPUtils\Mapping\Generator\Extractor\Writer;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use PhpParser\PrettyPrinter\Standard;
use stdClass;
use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Extractor\Extractor;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\CompositeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

class FromObjectTransformation extends AbstractToObjectTransformation
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

        $extractor = new Extractor();
        $prototypeMethod = $this->tFactoryCtx->reflectionMethod;

        $propertiesToSet = $this->getPropertiesToSet($prototypeMethod, $reflectionClass, $methodVarScope);

        // read sources. put in property vars
        $readSourcesStmts = [];
        $builderFactory = new BuilderFactory();
        $parameters = $this->tFactoryCtx->map;
        $sourceParameter = $parameters->values()[0];
        foreach ($propertiesToSet as $propertyName => $property) {
            $mapping = $prototypeMethod->getMapping($propertyName);
            $propertyVar = $methodVarScope->getTargetPropertyVar($property);

            foreach ($parameters as $parameterName => $parameter) {
                if ($parameterName === $mapping->source) {
                    $readSourcesStmts[] = $builderFactory->assign(
                        $propertyVar,
                        $expr
                    );
                    break 2;
                }
            }

            $sourcePropertyName = $mapping->source;
            $sourceTypeInfo = $sourceParameter->getTypeInfo();
            $sourceClassName = $sourceTypeInfo[0]->getClassName() ?? stdClass::class;
            $sourceReader = $extractor->getReader($sourceClassName, $sourcePropertyName);

            if (!$sourceReader instanceof Reader) {
                // todo double check
                continue;
            }

            if ($mapping->qualifier instanceof Qualifier) {
                $reflectionPropertyMapperMethod = $prototypeMethod->getMapperMethod($mapping->qualifier);
                if ($reflectionPropertyMapperMethod->class === $prototypeMethod->class) {
                    // same class method
                    $readSourcesStmts[] = $builderFactory->assign(
                        $propertyVar,
                        $builderFactory->methodCall(
                            $builderFactory->this(),
                            $reflectionPropertyMapperMethod->name,
                            [
                                $builderFactory->argument($sourceReader->getExpr($expr)),
                            ]
                        )
                    );
                } else {
                    // external mapper
                    $externalMapperPropertyName = $classBuilder->injectProperty($reflectionPropertyMapperMethod->class);

                    $readSourcesStmts[] = $builderFactory->assign(
                        $propertyVar,
                        $builderFactory->methodCall(
                            $builderFactory->propertyFetch($builderFactory->this(), $externalMapperPropertyName),
                            $reflectionPropertyMapperMethod->name,
                            [
                                $builderFactory->argument($sourceReader->getExpr($expr)),
                            ]
                        )
                    );
                }
            } else {
                if ($sourceClassName === stdClass::class) {
                    // todo double check
                    $sourcePropertyTypeInfo = $property->getTypeInfo();
                } else {
                    $sourcePropertyTypeInfo = ReflectionProperty::fromClassAndName(
                        $sourceClassName,
                        $sourcePropertyName
                    )->getTypeInfo();
                }

                if ($sourcePropertyTypeInfo == $property->getTypeInfo()) {
                    $exprToBeAssigned = $sourceReader->getExpr($expr);
                } else {
                    $transformation = $this->compositeTFactory->getTransformation(
                        [$sourcePropertyTypeInfo],
                        $property->getTypeInfo(),
                        new TFactoryCtx(
                            new Map(),
                            $prototypeMethod
                        )
                    );

                    $exprToBeAssigned = $transformation->transform(
                        $sourceReader->getExpr($expr),
                        $methodVarScope,
                        $method,
                        $classBuilder
                    );
                }

                $readSourcesStmts[] = $builderFactory->assign(
                    $propertyVar,
                    $exprToBeAssigned
                );
            }
        }

        $method->addStmts($readSourcesStmts);

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
            throw new InvalidConfigException('Unknown properties: ' . implode(', ', $propertiesToSet->keys()));
        }

        return $methodVarScope->getTargetVar();
    }
}
