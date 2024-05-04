<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Reflection\Mixins;

use Traversable;
use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\ReflectionException;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;
use VKPHPUtils\Mapping\Reflection\ReflectionParameter;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;
use VKPHPUtils\Mapping\Types\Extractor\ITypesExtractor;

trait TypeInfoAware
{
    private readonly ITypesExtractor $typesExtractor;

    /**
     * @return Type[]
     */
    final public function getTypeInfo(): array
    {
        if ($this instanceof ReflectionClass) {
            return [
                new Type(Type::BUILTIN_TYPE_OBJECT, false, $this->name, $this instanceof Traversable),
            ];
        }

        if ($this instanceof ReflectionMethod) {
            if ($this->hasTargetParameter()) {
                return $this->typesExtractor->getParameterTypes($this->getTargetParameter());
            }

            return $this->typesExtractor->getMethodReturnTypes($this);
        }

        if ($this instanceof ReflectionProperty) {
            return $this->typesExtractor->getPropertyTypes($this);
        }

        if ($this instanceof ReflectionParameter) {
            return $this->typesExtractor->getParameterTypes($this);
        }

        throw new ReflectionException('Undefined behaviour');
    }
}
