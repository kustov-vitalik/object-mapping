<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Extractor;

use Symfony\Component\PropertyInfo\PropertyReadInfo;
use stdClass;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyWriteInfo;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Helper\ClassASTHolder;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

class Extractor
{
    public function __construct(
        private readonly ReflectionExtractor $reflectionExtractor = new ReflectionExtractor(),
        private readonly BuilderFactory $builderFactory = new BuilderFactory(),
    ) {
    }

    public function getReader(string $sourceClassName, string $property): ?Reader
    {
        if ($sourceClassName === 'array') {
            return new Reader(ReaderType::ARRAY_DIM, $property, $this->builderFactory);
        }

        if ($sourceClassName === stdClass::class) {
            return new Reader(ReaderType::PROPERTY, $property, $this->builderFactory);
        }


        $propertyReadInfo = $this->reflectionExtractor->getReadInfo($sourceClassName, $property);
        if (!$propertyReadInfo instanceof PropertyReadInfo) {
            return null;
        }

        if ($propertyReadInfo->getType() === 'property') {
            return new Reader(ReaderType::PROPERTY, $propertyReadInfo->getName(), $this->builderFactory);
        }

        if ($propertyReadInfo->getType() === 'method') {
            return new Reader(ReaderType::GETTER, $propertyReadInfo->getName(), $this->builderFactory);
        }

        return null;
    }

    public function getWriter(string $target, string $property): ?Writer
    {
        if ($target === 'array') {
            return new Writer(WriterType::ARRAY_DIM, $property, $this->builderFactory);
        }

        $propertyWriteInfo = $this->reflectionExtractor->getWriteInfo($target, $property);

        if ($propertyWriteInfo instanceof PropertyWriteInfo) {
            if ($propertyWriteInfo->getType() === PropertyWriteInfo::TYPE_ADDER_AND_REMOVER) {
                return new Writer(WriterType::ADDER_REMOVER, $propertyWriteInfo->getName(), $this->builderFactory);
            }

            if ($propertyWriteInfo->getType() === PropertyWriteInfo::TYPE_CONSTRUCTOR) {
                return new Writer(WriterType::CONSTRUCTOR, $propertyWriteInfo->getName(), $this->builderFactory);
            }

            if ($propertyWriteInfo->getType() === PropertyWriteInfo::TYPE_PROPERTY) {
                return new Writer(WriterType::PROPERTY, $propertyWriteInfo->getName(), $this->builderFactory);
            }

            if ($propertyWriteInfo->getType() === PropertyWriteInfo::TYPE_METHOD) {
                return new Writer(WriterType::SETTER, $propertyWriteInfo->getName(), $this->builderFactory);
            }
        }


        $reflectionClass = ReflectionClass::forName($target);

        $classASTHolder = ClassASTHolder::forClass($reflectionClass);
        if ($reflectionClass->hasProperty($property)) {
            $reflectionProperty = ReflectionProperty::fromReflectionProperty($reflectionClass->getProperty($property));
            $propertyAST = $classASTHolder->findPropertyAST($reflectionProperty);
            if ($propertyAST instanceof Property) {
                if ($propertyAST->isPublic()) {
                    return new Writer(WriterType::PROPERTY, $property);
                }

                $setterAST = $classASTHolder->findPublicSetterMethodAST($reflectionProperty);
                if ($setterAST instanceof ClassMethod) {
                    return new Writer(WriterType::SETTER, $property);
                }

                $constructorAST = $classASTHolder->findConstructorAST();
                if ($constructorAST instanceof ClassMethod) {
                    throw new NotImplementedYet(__METHOD__);
                }
            }
        }

        return null;
    }
}
