<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\InitiationCode;

use VKPHPUtils\Mapping\Generator\IInitiationCodeGenerator;

class CachedInitiationCodeGenerator implements IInitiationCodeGenerator
{
    private array $cache = [];

    public function __construct(private readonly IInitiationCodeGenerator $initiationCodeGenerator)
    {
    }

    public function generateInitiationCode(string $instanceClassName): array
    {
        return $this->cache[$instanceClassName]
            ?? ($this->cache[$instanceClassName] = $this->initiationCodeGenerator->generateInitiationCode(
                $instanceClassName
            ));
    }
}
