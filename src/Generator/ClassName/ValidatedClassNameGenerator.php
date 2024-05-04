<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\ClassName;

use VKPHPUtils\Mapping\Generator\IClassNameGenerator;
use VKPHPUtils\Mapping\Generator\IValidator;

final readonly class ValidatedClassNameGenerator implements IClassNameGenerator
{
    public function __construct(
        private IClassNameGenerator $mapperClassNameGenerator,
        private IValidator $mapperValidator,
    ) {
    }

    public function generateMapperClassName(string $mapperClassName): string
    {
        $this->mapperValidator->validate();

        return $this->mapperClassNameGenerator->generateMapperClassName($mapperClassName);
    }
}
