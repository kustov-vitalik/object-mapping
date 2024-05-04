<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\InitiationCode;

use Exception;
use VKPHPUtils\Mapping\Generator\IInitiationCodeGenerator;

final class CompositeInitiationCodeGenerator implements IInitiationCodeGenerator
{
    private readonly array $generators;


    public function __construct(IInitiationCodeGenerator...$generators)
    {
        $this->generators = $generators;
    }

    public function generateInitiationCode(string $instanceClassName): array
    {
        foreach ($this->generators as $generator) {
            try {
                return $generator->generateInitiationCode($instanceClassName);
            } catch (Exception) {
            }
        }

        throw new Exception();
    }
}
