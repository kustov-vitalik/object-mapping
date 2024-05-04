<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToObject;

use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Generator\Extractor\Reader;
use VKPHPUtils\Mapping\Generator\Extractor\Writer;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use stdClass;
use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Extractor\Extractor;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\CompositeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Helper\TypeChecker;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

class FromManySourcesObjectTransformation extends AbstractToObjectTransformation
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

        $assignedProps = [];

        foreach ($this->tFactoryCtx->map as $sourceParameterName => $sourceParameter) {
            $sourceParameterVar = $methodVarScope->getParameterVar($sourceParameter);

            $types = $sourceParameter->getTypeInfo();
            $typeCount = count($types);
            if ($typeCount === 0) {
                throw new NotImplementedYet(__METHOD__);
            }

            if ($typeCount > 1) {
                throw new NotImplementedYet(__METHOD__);
            }

            $type = reset($types);
            if (TypeChecker::isObjectType($type)) {
                foreach ($propertiesToSet as $propertyName => $property) {
                    $mapping = $prototypeMethod->getMapping($propertyName);
                    $propertyVar = $methodVarScope->getTargetPropertyVar($property);

                    if ($sourceParameterName === $mapping->source) {
                        $targetTypeInfo = $property->getTypeInfo();
                        if (count($targetTypeInfo) !== 1) {
                            throw new NotImplementedYet(__METHOD__);
                        }

                        $targetType = reset($targetTypeInfo);

                        if (TypeChecker::equals($type, $targetType)) {
                            $assignExpr = $builderFactory->assign($propertyVar, $sourceParameterVar);
                            if (!isset($assignedProps[$property->name])) {
                                $readSourcesStmts[] = $assignExpr;

                                $assignedProps[$property->name] = true;
                            } else {
                                $readSourcesStmts[] = $builderFactory->if(
                                    new Identical(
                                        $methodVarScope->getTargetPropertyVar($property),
                                        $builderFactory->val(null)
                                    ),
                                    [
                                        $builderFactory->expression($assignExpr)
                                    ]
                                );
                            }

                            continue;
                        }
                    }


                    $sourceTypeInfo = $sourceParameter->getTypeInfo();
                    $sourceClassName = $sourceTypeInfo[0]->getClassName() ?? stdClass::class;
                    $sourcePropertyName = $mapping->source;


                    $sourceReader = $extractor->getReader($sourceClassName, $sourcePropertyName);
                    if (!$sourceReader instanceof Reader) {
                        // means source class does not have the property. skip loop cycle.
                        continue;
                    }


                    if ($sourceClassName === stdClass::class) {
                        // todo double check
                        $sourcePropertyTypeInfo = $property->getTypeInfo();
                    } else {
                        $sourcePropertyTypeInfo = ReflectionProperty::fromClassAndName(
                            $sourceClassName,
                            $sourcePropertyName
                        )->getTypeInfo();
                    }

                    $transformation = $this->compositeTFactory->getTransformation(
                        [$sourcePropertyTypeInfo],
                        $property->getTypeInfo(),
                        new TFactoryCtx(
                            new Map(),
                            $prototypeMethod
                        )
                    );

                    $assignExpr = $builderFactory->assign(
                        $propertyVar,
                        $transformation->transform(
                            $sourceReader->getExpr($sourceParameterVar),
                            $methodVarScope,
                            $method,
                            $classBuilder
                        )
                    );

                    if (!isset($assignedProps[$property->name])) {
                        $readSourcesStmts[] = $assignExpr;

                        $assignedProps[$property->name] = true;
                    } else {
                        $readSourcesStmts[] = $builderFactory->if(
                            new Identical(
                                $methodVarScope->getTargetPropertyVar($property),
                                $builderFactory->val(null)
                            ),
                            [
                                $builderFactory->expression($assignExpr)
                            ]
                        );
                    }
                }
            } elseif (TypeChecker::isScalarType($type)) {
                foreach ($propertiesToSet as $propertyName => $property) {
                    $mapping = $prototypeMethod->getMapping($property->name);
                    if ($mapping->source !== $sourceParameterName) {
                        continue;
                    }

                    if ($mapping->qualifier instanceof Qualifier) {
                        $mapperMethod = $prototypeMethod->getMapperMethod($mapping->qualifier);

                        if ($mapperMethod->class === $prototypeMethod->class) {
                            // same class
                            $mapper = $builderFactory->this();
                        } else {
                            // external mapper
                            $mapperPropertyName = $classBuilder->injectProperty($mapperMethod->class);
                            $mapper = $builderFactory->propertyFetch($builderFactory->this(), $mapperPropertyName);
                        }

                        $assign = $builderFactory->assign(
                            $methodVarScope->getTargetPropertyVar($property),
                            $builderFactory->methodCall(
                                $mapper, $mapperMethod->name, [
                                $builderFactory->argument($sourceParameterVar)
                                ]
                            )
                        );
                    } else {
                        $transformation = $this->compositeTFactory->getTransformation(
                            [$sourceParameter->getTypeInfo()],
                            $property->getTypeInfo(),
                            new TFactoryCtx(
                                new Map(),
                                $prototypeMethod
                            )
                        );

                        $assign = $builderFactory->assign(
                            $methodVarScope->getTargetPropertyVar($property),
                            $transformation->transform(
                                $sourceParameterVar,
                                $methodVarScope,
                                $method,
                                $classBuilder
                            )
                        );
                    }


                    if (!isset($assignedProps[$property->name])) {
                        $readSourcesStmts[] = $assign;
                        $assignedProps[$propertyName] = true;
                    } else {
                        $readSourcesStmts[] = $builderFactory->if(
                            new Identical(
                                $methodVarScope->getTargetPropertyVar($property),
                                $builderFactory->val(null)
                            ),
                            [$builderFactory->expression($assign)]
                        );
                    }
                }
            } elseif (TypeChecker::isArrayAccessType($type) || TypeChecker::isArrayType($type)) {
                // todo get values from array

                foreach ($propertiesToSet as $propertyName => $property) {
                    $mapping = $prototypeMethod->getMapping($propertyName);
                    $propertyVar = $methodVarScope->getTargetPropertyVar($property);
                    $sourcePropertyName = $mapping->source;

                    $sourceReader = $extractor->getReader('array', $sourcePropertyName);

                    $sourcePropertyTypeInfo = $property->getTypeInfo();

                    $transformation = $this->compositeTFactory->getTransformation(
                        [$sourcePropertyTypeInfo],
                        $property->getTypeInfo(),
                        new TFactoryCtx(
                            new Map(),
                            $prototypeMethod
                        )
                    );

                    if ($sourceReader instanceof Reader) {
                        $assignTarget = $builderFactory->assign(
                            $propertyVar,
                            $transformation->transform(
                                $sourceReader->getExpr($sourceParameterVar),
                                $methodVarScope,
                                $method,
                                $classBuilder
                            )
                        );

                        if (isset($assignedProps[$property->name])) {
                            $readSourcesStmts[] = $builderFactory->if(
                                new Identical(
                                    $methodVarScope->getTargetPropertyVar($property),
                                    $builderFactory->val(null)
                                ),
                                [
                                    $builderFactory->expression(
                                        $assignTarget
                                    )
                                ]
                            );
                        } else {
                            $readSourcesStmts[] = $assignTarget;
                            $assignedProps[$property->name] = true;
                        }
                    }
                }
            } else {
                throw new NotImplementedYet(__METHOD__ . ' SOURCE TYPE: ' . print_r($type, true));
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
            throw new InvalidConfigException("Unknown properties: " . implode(', ', $propertiesToSet->keys()));
        }

        return $methodVarScope->getTargetVar();
    }
}
