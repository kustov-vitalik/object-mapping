<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Reflection;

use VKPHPUtils\Mapping\Attributes\Target;
use VKPHPUtils\Mapping\Exception\ReflectionException;
use VKPHPUtils\Mapping\Reflection\Mixins\AttributeAware;
use VKPHPUtils\Mapping\Reflection\Mixins\TypeInfoAware;
use VKPHPUtils\Mapping\Types\Extractor\FixCollectionExtractorDecorator;
use VKPHPUtils\Mapping\Types\Extractor\MergeTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\PhpDocTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\DocBlockTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\ReflectionTypesExtractor;

/**
 * @psalm-immutable
 */
class ReflectionParameter extends \ReflectionParameter
{
    use AttributeAware;
    use TypeInfoAware;

    public function __construct($function, int|string $param)
    {
        try {
            parent::__construct($function, $param);
            $this->typesExtractor = new FixCollectionExtractorDecorator(
                new MergeTypesExtractor(
                    new DocBlockTypesExtractor(),
                    //                    new PhpDocTypesExtractor(),
                    new ReflectionTypesExtractor(),
                ),
            );
        } catch (\ReflectionException $reflectionException) {
            throw ReflectionException::fromReflectionException($reflectionException);
        }
    }


    public static function fromReflectionParameter(\ReflectionParameter $reflectionParameter): ReflectionParameter
    {
        return new ReflectionParameter(
            [$reflectionParameter->getDeclaringClass()?->name, $reflectionParameter->getDeclaringFunction()->name], $reflectionParameter->name
        );
    }

    final public function isTarget(): bool
    {
        return $this->hasAttribute(Target::class);
    }
}
