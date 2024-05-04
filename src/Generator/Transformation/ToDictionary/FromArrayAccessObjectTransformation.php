<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToDictionary;

use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Extractor\Extractor;
use VKPHPUtils\Mapping\Generator\Transformation\ITransformation;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;

class FromArrayAccessObjectTransformation implements ITransformation
{
    public function __construct(
        private readonly ReflectionMethod $reflectionMethod,
    ) {
    }

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        $mappings = $this->reflectionMethod->getMappings();
        if ($mappings === []) {
            throw new InvalidConfigException('Invalid config. At least one #[Mapping] is required.');
        }

        $extractor = new Extractor();
        $builderFactory = new BuilderFactory();

        $stmts = [];
        $targetVar = $methodVarScope->getTargetVar();

        if (!$this->reflectionMethod->hasTargetParameter()) {
            $stmts[] = $builderFactory->assign($targetVar, $builderFactory->array());
        }

        foreach ($mappings as $mapping) {
            if ($mapping->ignore) {
                continue;
            }

            $sourceReader = $extractor->getReader('array', $mapping->source);
            $targetReader = $extractor->getReader('array', $mapping->target);
            $targetWriter = $extractor->getWriter('array', $mapping->target);

            $stmts[] = $targetWriter->getExpr($targetVar, $sourceReader->getExpr($expr));
        }


        $method->addStmts($stmts);


        return $methodVarScope->getTargetVar();
    }
}
