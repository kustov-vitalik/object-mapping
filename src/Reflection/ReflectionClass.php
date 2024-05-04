<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Reflection;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Exception\ReflectionException;
use VKPHPUtils\Mapping\Reflection\Mixins\AttributeAware;
use VKPHPUtils\Mapping\Reflection\Mixins\TypeInfoAware;
use VKPHPUtils\Mapping\Types\Extractor\FixCollectionExtractorDecorator;
use VKPHPUtils\Mapping\Types\Extractor\MergeTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\PhpDocTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\DocBlockTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\ReflectionTypesExtractor;

class ReflectionClass extends \ReflectionClass
{
    use AttributeAware;
    use TypeInfoAware;

    /**
     * @param object|class-string $objectOrClass
     */
    public function __construct(object|string $objectOrClass)
    {
        try {
            parent::__construct($objectOrClass);
            $this->typesExtractor = new FixCollectionExtractorDecorator(
                new MergeTypesExtractor(
                    new DocBlockTypesExtractor(),
                    new PhpDocTypesExtractor(),
                    new ReflectionTypesExtractor(),
                )
            );
        } catch (\ReflectionException $reflectionException) {
            throw ReflectionException::fromReflectionException($reflectionException);
        }
    }


    /**
     * @param class-string $className
     */
    public static function forName(string $className): ReflectionClass
    {
        return new ReflectionClass($className);
    }

    final public function isClass(): bool
    {
        if ($this->isInterface() || $this->isTrait()) {
            return false;
        }

        return !(PHP_VERSION_ID >= 80100 && $this->isEnum());
    }

    final public function isAbstractClass(): bool
    {
        return $this->isClass() && $this->isAbstract();
    }

    final public function isMapper(): bool
    {
        return $this->hasAttribute(Mapper::class);
    }

    /**
     * @return ReflectionMethod[]
     */
    final public function getMapperMethods(): array
    {
        return array_map(
            static fn(\ReflectionMethod $reflectionMethod): ReflectionMethod => ReflectionMethod::fromReflectionMethod($reflectionMethod),
            array_filter(
                $this->getMethods(),
                static fn(\ReflectionMethod $reflectionMethod): bool => $reflectionMethod->isAbstract() && $reflectionMethod->isPublic()
                    && !$reflectionMethod->isConstructor() && $reflectionMethod->isUserDefined() && !$reflectionMethod->isStatic()
            )
        );
    }

    /**
     * @return ReflectionProperty[]
     */
    final public function getMapperProperties(): array
    {
        return array_map(
            static fn(\ReflectionProperty $reflectionProperty): ReflectionProperty => ReflectionProperty::fromReflectionProperty($reflectionProperty),
            $this->getProperties()
        );
    }

    final public function findMapperMethodNamedWith(string $methodName): ReflectionMethod|null
    {
        foreach ($this->getMethods() as $reflectionMethod) {
            $method = ReflectionMethod::fromReflectionMethod($reflectionMethod);
            if ($method->getNamed()->name === $methodName) {
                return $method;
            }
        }

        return null;
    }

    final public function getMapperMethod(string $methodName): ReflectionMethod
    {
        if (!$this->hasMethod($methodName)) {
            throw new ReflectionException(sprintf('Method "%s" not found in class "%s"', $methodName, $this->name));
        }

        return ReflectionMethod::fromReflectionMethod($this->getMethod($methodName));
    }
}
