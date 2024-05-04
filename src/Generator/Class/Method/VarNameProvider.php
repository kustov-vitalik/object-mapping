<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class\Method;

class VarNameProvider
{
    private array $registry = [];

    public function getUniqueName(string $name): string
    {
        if (!isset($this->registry[$name])) {
            $this->registry[$name] = 0;

            return $name;
        }

        ++$this->registry[$name];

        return sprintf('%s_%s', $name, $this->registry[$name]);
    }
}
