<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\InitiationCode;

use VKPHPUtils\Mapping\Generator\IInitiationCodeGenerator;
use VKPHPUtils\Mapping\Generator\IValidator;

final readonly class ValidatedInitiationCodeGenerator implements IInitiationCodeGenerator
{
    public function __construct(
        private IInitiationCodeGenerator $initiationCodeGenerator,
        private IValidator $mapperValidator,
    ) {
    }

    public function generateInitiationCode(string $instanceClassName): array
    {
        $this->mapperValidator->validate();

        return $this->initiationCodeGenerator->generateInitiationCode($instanceClassName);
    }
}
