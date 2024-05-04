<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Reflection;

use VKPHPUtils\Mapping\Exception\ReflectionException;
use VKPHPUtils\Mapping\Reflection\Mixins\TypeInfoAware;
use VKPHPUtils\Mapping\Types\Extractor\FixCollectionExtractorDecorator;
use VKPHPUtils\Mapping\Types\Extractor\MergeTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\PhpDocTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\DocBlockTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\ReflectionTypesExtractor;

class ReflectionProperty extends \ReflectionProperty
{
    use TypeInfoAware;

    /**
     * @param object|class-string $class
     */
    public function __construct(object|string $class, string $property)
    {
        try {
            parent::__construct($class, $property);
            $this->typesExtractor = new FixCollectionExtractorDecorator(
                new MergeTypesExtractor(
                    new DocBlockTypesExtractor(),
                    //                    new PhpDocTypesExtractor(),
                    new ReflectionTypesExtractor(),
                )
            );
        } catch (\ReflectionException $reflectionException) {
            throw ReflectionException::fromReflectionException($reflectionException);
        }
    }

    public static function fromReflectionProperty(\ReflectionProperty $reflectionProperty): ReflectionProperty
    {
        try {
            return new ReflectionProperty($reflectionProperty->class, $reflectionProperty->name);
        } catch (\ReflectionException $reflectionException) {
            throw ReflectionException::fromReflectionException($reflectionException);
        }
    }

    /**
     * @param class-string $class
     */
    public static function fromClassAndName(string $class, string $name): ReflectionProperty
    {
        try {
            return new ReflectionProperty($class, $name);
        } catch (\ReflectionException $reflectionException) {
            throw ReflectionException::fromReflectionException($reflectionException);
        }
    }
}
