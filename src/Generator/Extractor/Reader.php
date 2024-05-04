<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Extractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use VKPHPUtils\Mapping\Exception\RuntimeException;
use VKPHPUtils\Mapping\Helper\BuilderFactory;

final readonly class Reader
{
    public function __construct(
        private ReaderType $readerType,
        private string $property,
        private BuilderFactory $builderFactory = new BuilderFactory(),
    ) {
    }

    public function getExpr(Variable $variable): Expr
    {
        if ($this->readerType === ReaderType::GETTER) {
            return $this->builderFactory->methodCall($variable, $this->property);
        }

        if ($this->readerType === ReaderType::ARRAY_DIM) {
            return $this->builderFactory->coalesce(
                $this->builderFactory->arrayDimFetch($variable, $this->builderFactory->val($this->property)),
                $this->builderFactory->val(null)
            );
        }

        if ($this->readerType === ReaderType::PROPERTY) {
            return $this->builderFactory->propertyFetch($variable, $this->property);
        }

        throw new RuntimeException();
    }
}
