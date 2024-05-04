<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class\Method;

use PhpParser\Node\Expr\Variable;
use PhpParser\Builder\Method;
use VKPHPUtils\Mapping\Attributes\ToEnum;
use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\IMethodGenerator;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\CompositeTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromManySources\ManyToDictionaryTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromManySources\ManyToObjectTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToArrayTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToBooleanTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToDictionaryTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToEnumTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToFloatTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToGeneratorTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToIntTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToIterableTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToIteratorAggregateTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToIteratorTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToObjectTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToResourceTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToStringTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\Factory\FromSingleSource\ToTraversableTFactory;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Helper\TypeMapper;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;

final readonly class MethodGenerator implements IMethodGenerator
{

    public function __construct(
        private ClassBuilder $classBuilder,
        private IParameterGenerator $parameterGenerator,
    ) {
    }

    public function generateMapperClassMethod(ReflectionMethod $reflectionMethod): Method
    {
        $method = $this->classBuilder->createMethod($reflectionMethod->name);
        $this->methodSignature($method, $reflectionMethod);

        $map = new Map();
        $sourceTypeInfos = [];
        foreach ($reflectionMethod->getMapperParameters() as $reflectionParameter) {
            if ($reflectionParameter->isTarget()) {
                continue;
            }

            $map[$reflectionParameter->name] = $reflectionParameter;
            $sourceTypeInfos[] = $reflectionParameter->getTypeInfo();
        }

        $compositeTFactory = new CompositeTFactory();

        $compositeTFactory->addTransformationFactory(
            new ToEnumTFactory(
                $reflectionMethod->hasToEnum() ? $reflectionMethod->getToEnum()->mapEnumStrategy : (new ToEnum())->mapEnumStrategy
            )
        );
        $compositeTFactory->addTransformationFactory(new ToStringTFactory());
        $compositeTFactory->addTransformationFactory(new ToIntTFactory());
        $compositeTFactory->addTransformationFactory(new ToFloatTFactory());
        $compositeTFactory->addTransformationFactory(new ToBooleanTFactory());
        $compositeTFactory->addTransformationFactory(new ToResourceTFactory());
        $compositeTFactory->addTransformationFactory(new ToDictionaryTFactory($compositeTFactory, $reflectionMethod));
        $compositeTFactory->addTransformationFactory(new ToGeneratorTFactory($reflectionMethod));
        $compositeTFactory->addTransformationFactory(new ToIteratorAggregateTFactory($reflectionMethod));
        $compositeTFactory->addTransformationFactory(new ToIteratorTFactory($reflectionMethod));
        $compositeTFactory->addTransformationFactory(new ToTraversableTFactory($reflectionMethod));
        $compositeTFactory->addTransformationFactory(new ToArrayTFactory($reflectionMethod));
        $compositeTFactory->addTransformationFactory(new ToIterableTFactory($reflectionMethod));
        $compositeTFactory->addTransformationFactory(new ToObjectTFactory($compositeTFactory));
        $compositeTFactory->addTransformationFactory(new ManyToDictionaryTFactory($compositeTFactory, $reflectionMethod));
        $compositeTFactory->addTransformationFactory(new ManyToObjectTFactory($compositeTFactory));


        $transformation = $compositeTFactory->getTransformation(
            $sourceTypeInfos,
            $reflectionMethod->getTypeInfo(),
            new TFactoryCtx($map, $reflectionMethod)
        );

        $methodVarScope = new MethodVarScope($reflectionMethod);
        $input = null;
        if ($methodVarScope->getParametersCount() === 1) {
            $input = $methodVarScope->getTheOnlyParameterVar();
        } else {
            foreach ($reflectionMethod->getMapperParameters() as $reflectionParameter) {
                if ($reflectionParameter->isTarget()) {
                    continue;
                }

                $input = $methodVarScope->getParameterVar($reflectionParameter);
                break;
            }
        }

        if (!$input instanceof Variable) {
            throw new InvalidConfigException('No parameter found');
        }

        $output = $transformation->transform($input, $methodVarScope, $method, $this->classBuilder);

        $builderFactory = new BuilderFactory();
        if (!$reflectionMethod->hasTargetParameter()) {
            $method->addStmt($builderFactory->return($output));
        }

        return $method;
    }

    private function methodSignature(Method $method, ReflectionMethod $reflectionMethod): void
    {
        $method->makePublic();

        if ($reflectionMethod->returnsReference()) {
            $method->makeReturnByRef();
        }

        foreach ($reflectionMethod->getMapperParameters() as $reflectionParameter) {
            $method->addParam(
                $this->parameterGenerator->generateMapperClassMethodParameter($reflectionParameter)
            );
        }

        if ($reflectionMethod->hasReturnType()) {
            $method->setReturnType((new TypeMapper())->mapType($reflectionMethod->getReturnType()));
        }
    }

}
