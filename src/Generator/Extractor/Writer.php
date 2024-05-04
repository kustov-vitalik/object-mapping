<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Extractor;

use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use VKPHPUtils\Mapping\Exception\RuntimeException;
use VKPHPUtils\Mapping\Helper\BuilderFactory;

final readonly class Writer
{
    public function __construct(
        private WriterType $writerType,
        private string $property,
        private BuilderFactory $builderFactory = new BuilderFactory(),
    ) {
    }

    public function getExpr(Variable $variable, Expr $expr, bool $byRef = false): Expr
    {
        if ($this->writerType === WriterType::SETTER || $this->writerType === WriterType::ADDER_REMOVER) {
            return $this->builderFactory->methodCall($variable, $this->property, [$this->builderFactory->argument($expr, $byRef)]);
        }

        if ($this->writerType === WriterType::PROPERTY) {
            if ($byRef) {
                return $this->builderFactory->assignByRef(
                    $this->builderFactory->propertyFetch($variable, $this->property),
                    $expr
                );
            }

            return $this->builderFactory->assign(
                $this->builderFactory->propertyFetch($variable, $this->property),
                $expr
            );
        }

        if ($this->writerType === WriterType::ARRAY_DIM) {
            if ($byRef) {
                return $this->builderFactory->assignByRef(
                    new ArrayDimFetch($variable, $this->builderFactory->val($this->property)),
                    $expr
                );
            }

            return $this->builderFactory->assign(
                new ArrayDimFetch($variable, $this->builderFactory->val($this->property)),
                $expr
            );
        }

        throw new RuntimeException();
    }
}
