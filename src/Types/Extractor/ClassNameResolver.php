<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types\Extractor;

final readonly class ClassNameResolver
{
    public function __construct(private string $calledClassName, private string $namespace, private array $uses = [])
    {
    }

    public function resolveRootClass(): string
    {
        return $this->resolveStringName($this->calledClassName);
    }

    public function resolveStringName(string $name): string
    {
        if (str_starts_with($name, '\\')) {
            return ltrim($name, '\\');
        }

        $nameParts = explode('\\', $name);
        $firstNamePart = $nameParts[0];
        if (isset($this->uses[$firstNamePart])) {
            if (1 === \count($nameParts)) {
                return $this->uses[$firstNamePart];
            }

            array_shift($nameParts);

            return sprintf('%s\\%s', $this->uses[$firstNamePart], implode('\\', $nameParts));
        }

        return sprintf('%s\\%s', $this->namespace, $name);
    }
}
