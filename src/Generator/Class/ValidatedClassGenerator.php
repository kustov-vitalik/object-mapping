<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class;

use PhpParser\Node;
use VKPHPUtils\Mapping\Generator\IClassGenerator;
use VKPHPUtils\Mapping\Generator\IValidator;

final readonly class ValidatedClassGenerator implements IClassGenerator
{
    public function __construct(
        private IClassGenerator $mapperClassGenerator,
        private IValidator $mapperValidator,
    ) {
    }

    public function generateMapperClass(): Node
    {
        $this->mapperValidator->validate();
        return $this->mapperClassGenerator->generateMapperClass();
    }
}
