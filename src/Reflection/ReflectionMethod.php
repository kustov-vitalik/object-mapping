<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Reflection;

use LogicException;
use RuntimeException;
use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Attributes\Mapping;
use VKPHPUtils\Mapping\Attributes\Named;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Attributes\ToCollection;
use VKPHPUtils\Mapping\Attributes\ToDictionary;
use VKPHPUtils\Mapping\Attributes\ToEnum;
use VKPHPUtils\Mapping\Exception\InvalidConfigException;
use VKPHPUtils\Mapping\Exception\MapperMethodNotFoundException;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\ReflectionException;
use VKPHPUtils\Mapping\Helper\TypeChecker;
use VKPHPUtils\Mapping\Reflection\Mixins\AttributeAware;
use VKPHPUtils\Mapping\Reflection\Mixins\TypeInfoAware;
use VKPHPUtils\Mapping\Types\Extractor\FixCollectionExtractorDecorator;
use VKPHPUtils\Mapping\Types\Extractor\MergeTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\PhpDocTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\DocBlockTypesExtractor;
use VKPHPUtils\Mapping\Types\Extractor\ReflectionTypesExtractor;

class ReflectionMethod extends \ReflectionMethod
{
    use AttributeAware;
    use TypeInfoAware;

    /**
     * @var array<string, Mapping> 
     */
    private array $mappings = [];


    public static function fromReflectionMethod(\ReflectionMethod $reflectionMethod): ReflectionMethod
    {
        return new ReflectionMethod($reflectionMethod->class, $reflectionMethod->name);
    }

    public function __construct(object|string $objectOrMethod, ?string $method = null)
    {
        try {
            parent::__construct($objectOrMethod, $method);
            $this->typesExtractor = new FixCollectionExtractorDecorator(
                new MergeTypesExtractor(
                    new DocBlockTypesExtractor(),
                    //                    new PhpDocTypesExtractor(),
                    new ReflectionTypesExtractor(),
                )
            );
            $this->fillMappings();
        } catch (\ReflectionException $reflectionException) {
            throw ReflectionException::fromReflectionException($reflectionException);
        }
    }

    public function getMapperMethod(Qualifier $qualifier): ReflectionMethod
    {
        $methodName = $qualifier->method;
        $className = $qualifier->class ?? $this->class;
        $protoClass = ReflectionClass::forName($className);

        return $protoClass->findMapperMethodNamedWith($methodName) ?? $protoClass->getMapperMethod($methodName);
    }

    public function getToCollectionMapperMethod(): ReflectionMethod
    {
        if (!$this->hasToCollection()) {
            throw new LogicException("This method doesn't have #[ToCollection] attribute");
        }

        $qualifier = $this->getToCollection()->qualifier;
        if ($qualifier instanceof Qualifier) {
            return $this->getMapperMethod($qualifier);
        }

        $targetTypeInfo = $this->getTypeInfo();
        if (count($targetTypeInfo) !== 1) {
            throw new NotImplementedYet(__METHOD__);
        }

        $targetType = reset($targetTypeInfo);
        if (!$targetType instanceof Type) {
            throw new RuntimeException(__METHOD__);
        }

        $sourceTypeInfo = $this->getTheOnlyParameter()->getTypeInfo();
        if ($sourceTypeInfo === []) {
            throw new NotImplementedYet(__METHOD__);
        }

        if (count($sourceTypeInfo) > 1) {
            throw new NotImplementedYet(__METHOD__);
        }

        $sourceType = reset($sourceTypeInfo);
        if (!$sourceType instanceof Type) {
            throw new RuntimeException(__METHOD__);
        }

        $sourceIterableItemValueTypes = $sourceType->getCollectionValueTypes();
        if ($sourceIterableItemValueTypes === []) {
            throw new NotImplementedYet(__METHOD__);
        }

        if (count($sourceIterableItemValueTypes) > 1) {
            throw new NotImplementedYet(__METHOD__);
        }

        /**
 * @var Type $sourceIterableItemValueType 
*/
        $sourceIterableItemValueType = reset($sourceIterableItemValueTypes);
        $targetIterableItemValueTypes = $targetType->getCollectionValueTypes();
        if (count($targetIterableItemValueTypes) !== 1) {
            throw new NotImplementedYet(__METHOD__);
        }

        /**
 * @var Type $targetIterableItemValueType 
*/
        $targetIterableItemValueType = $targetIterableItemValueTypes[0];

        $mapperClass = ReflectionClass::forName($this->class);

        /**
 * @var ReflectionMethod[] $candidatesByReturnType 
*/
        $candidatesByReturnType = [];
        foreach ($mapperClass->getMapperMethods() as $reflectionMethod) {
            $candidateMethodTypeInfo = $reflectionMethod->getTypeInfo();
            foreach ($candidateMethodTypeInfo as $type) {
                if (TypeChecker::isSubtype($targetIterableItemValueType, $type)) {
                    $candidatesByReturnType[] = $reflectionMethod;
                }
            }
        }

        $candidatesBySourceType = [];
        foreach ($candidatesByReturnType as $candidates) {
            foreach ($candidates->getMapperParameters() as $parameter) {
                foreach ($parameter->getTypeInfo() as $type) {
                    if (TypeChecker::isSubtype($type, $sourceIterableItemValueType)) {
                        $candidatesBySourceType[] = $candidates;
                    }
                }
            }
        }

        if (count($candidatesBySourceType) > 1) {
            throw new NotImplementedYet(__METHOD__);
        }

        if ($candidatesBySourceType === []) {
            throw new MapperMethodNotFoundException($sourceIterableItemValueType, $targetIterableItemValueType, __METHOD__);
        }

        $mapperMethod = reset($candidatesBySourceType);
        if (!$mapperMethod instanceof ReflectionMethod) {
            throw new InvalidConfigException('Unable to auto locate mapper method. Bad config?');
        }

        return $mapperMethod;
    }

    public function hasTargetParameter(): bool
    {
        foreach ($this->getMapperParameters() as $reflectionParameter) {
            if ($reflectionParameter->isTarget()) {
                return true;
            }
        }

        return false;
    }

    public function getTargetParameter(): ReflectionParameter
    {
        foreach ($this->getMapperParameters() as $reflectionParameter) {
            if ($reflectionParameter->isTarget()) {
                return $reflectionParameter;
            }
        }

        throw new ReflectionException("No mapping target parameter");
    }

    public function hasToCollection(): bool
    {
        return $this->hasAttribute(ToCollection::class);
    }

    public function getToCollection(): ToCollection
    {
        return $this->getAttribute(ToCollection::class)->newInstance();
    }

    public function hasToDictionary(): bool
    {
        return $this->hasAttribute(ToDictionary::class);
    }

    public function getToDictionary(): ToDictionary
    {
        return $this->getAttribute(ToDictionary::class)->newInstance();
    }

    public function hasToEnum(): bool
    {
        return $this->hasAttribute(ToEnum::class);
    }

    public function getToEnum(): ToEnum
    {
        return $this->getAttribute(ToEnum::class)->newInstance();
    }


    /**
     * @return ReflectionParameter[]
     */
    public function getMapperParameters(): array
    {
        return array_map(
            static fn(\ReflectionParameter $reflectionParameter): ReflectionParameter => ReflectionParameter::fromReflectionParameter($reflectionParameter),
            $this->getParameters()
        );
    }

    public function getMapping(string $targetPropertyName): Mapping
    {
        return $this->mappings[$targetPropertyName]
            ?? $this->mappings[$targetPropertyName] = new Mapping($targetPropertyName);
    }

    public function findMappingBySource(string $sourcePropertyName): Mapping|null
    {
        foreach ($this->mappings as $mapping) {
            if ($mapping->source === $sourcePropertyName) {
                return $mapping;
            }
        }

        return null;
    }

    /**
     * @return Mapping[]
     */
    public function getMappings(): array
    {
        return $this->mappings;
    }

    public function getNamed(): Named
    {
        if ($this->hasAttribute(Named::class)) {
            $named = $this->getAttribute(Named::class)->newInstance();

            return new Named(
                $named->name ?? $this->getShortName()
            );
        }

        return new Named($this->getShortName());
    }

    private function fillMappings(): void
    {
        foreach ($this->getAttributes(Mapping::class) as $reflectionAttribute) {
            $mapping = $reflectionAttribute->newInstance();
            $this->mappings[$mapping->target] = $mapping;
        }
    }

    private function getTheOnlyParameter(): ReflectionParameter
    {
        $parameters = $this->getMapperParameters();

        if ($this->hasTargetParameter()) {
            if (count($parameters) !== 2) {
                throw new InvalidConfigException(
                    sprintf('Invalid IterableMapping config. Method: %s::%s', $this->class, $this->name)
                );
            }

            foreach ($parameters as $parameter) {
                if (!$parameter->isTarget()) {
                    return $parameter;
                }
            }

            throw new InvalidConfigException(
                sprintf('Invalid IterableMapping config. Method: %s::%s', $this->class, $this->name)
            );
        }

        if (count($parameters) !== 1) {
            throw new InvalidConfigException(
                sprintf('Invalid IterableMapping config. Method: %s::%s', $this->class, $this->name)
            );
        }

        return reset($parameters);
    }
}
