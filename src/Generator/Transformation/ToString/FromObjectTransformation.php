<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Transformation\ToString;

use PhpParser\Builder\Method;
use PhpParser\Node\Expr;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodVarScope;
use VKPHPUtils\Mapping\Generator\Extractor\Extractor;
use VKPHPUtils\Mapping\Generator\Transformation\TFactoryCtx;

class FromObjectTransformation extends AbstractToStringTransformation
{
    public function __construct(
        private readonly string $sourceClassName,
        private readonly TFactoryCtx $tFactoryCtx,
    ) {
    }

    public function transform(
        Expr $expr,
        MethodVarScope $methodVarScope,
        Method $method,
        ClassBuilder $classBuilder
    ): Expr {
        $prototypeMethod = $this->tFactoryCtx->reflectionMethod;
        $targetMapping = null;
        foreach ($prototypeMethod->getMappings() as $mapping) {
            if ($mapping->target === '' && $mapping->source !== '') {
                $targetMapping = $mapping;
                break;
            }
        }

        if (!$targetMapping) {
            throw new InvalidConfigException(
                sprintf("No mapping found for source '%s' and target 'string'", $this->sourceClassName)
            );
        }

        $extractor = new Extractor();
        $sourceReader = $extractor->getReader($this->sourceClassName, $targetMapping->source);
        $readExpr = $sourceReader->getExpr($expr);

        return parent::transform($readExpr, $methodVarScope, $method, $classBuilder);
    }
}
