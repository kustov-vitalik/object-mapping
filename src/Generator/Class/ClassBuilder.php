<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class;

use VKPHPUtils\Mapping\Generator\Extractor\Reader;
use VKPHPUtils\Mapping\Generator\Extractor\Writer;
use PhpParser\Builder\Method;
use PhpParser\Builder\Property;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_ as ClassStmt;
use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Constants;
use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\RuntimeException;
use VKPHPUtils\Mapping\Generator\Class\Method\VarNameProvider;
use VKPHPUtils\Mapping\Generator\Extractor\Extractor;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Helper\TypeChecker;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

class ClassBuilder
{

    private readonly Method $method;

    /**
     * @var array<string, Method> 
     */
    private array $methods = [];

    /**
     * @var array<string, Property> 
     */
    private array $properties = [];

    private FullyQualified $fullyQualified;

    /**
     * @var FullyQualified[] 
     */
    private array $implements = [];

    /**
     * @var Map<class-string, string> 
     */
    private readonly Map $map;

    public function __construct(
        private readonly string $fqcn
    ) {
        $this->method = $this->createMethod('__construct')->makePublic();
        $this->map = new Map();
    }

    public function build(): ClassStmt
    {
        $classBuilder = (new BuilderFactory())->class($this->fqcn);

        if (property_exists($this, 'fullyQualified') && isset($this->fullyQualified)) {
            $classBuilder->extend($this->fullyQualified);
        }

        foreach ($this->implements as $implement) {
            $classBuilder->implement($implement);
        }

        if (property_exists($this, 'method') && isset($this->method)) {
            $classBuilder->addStmt($this->method);
        }

        foreach ($this->methods as $name => $method) {
            if ($name === '__construct') {
                continue;
            }

            $classBuilder->addStmt($method);
        }

        foreach ($this->properties as $property) {
            $classBuilder->addStmt($property);
        }

        return $classBuilder->getNode();
    }

    public function createMethod(string $name): Method
    {
        if ($this->hasMethod($name)) {
            throw new RuntimeException(sprintf("Method %s already added to class %s", $name, $this->fqcn));
        }

        $method = (new BuilderFactory())->method($name);

        return $this->methods[$name] = $method;
    }

    public function hasMethod(string $name): bool
    {
        return array_key_exists($name, $this->methods);
    }

    public function extend(string $className): void
    {
        $this->fullyQualified = new FullyQualified($className);
    }

    public function implement(string...$classNames): void
    {
        foreach ($classNames as $className) {
            $this->implements[] = new FullyQualified($className);
        }
    }

    public function addProperty(Property $property): void
    {
        $propertyProperties = $property->getNode()->props;
        if (count($propertyProperties) !== 1) {
            throw new NotImplementedYet("Only single property declaration is supported");
        }

        $propertyProperty = reset($propertyProperties);
        $this->properties[$propertyProperty->name->toString()] = $property;
    }

    public function getConstructor(): Method
    {
        return $this->method;
    }

    /**
     * @param class-string $className
     */
    public function injectProperty(string $className): string
    {
        if ($this->map->containsKey($className)) {
            return $this->map->get($className);
        }

        $builderFactory = new BuilderFactory();
        $class = ReflectionClass::forName($className);

        $propertyName = $class->getShortName() . '_' . md5($className);

        $this->addProperty(
            $builderFactory->property($propertyName)
                ->makePrivate()
                ->makeReadonly()
                ->setType($builderFactory->fullyQualified($class->name))
        );

        $this->method->addStmt(
            $builderFactory->expression(
                $builderFactory->assign(
                    $builderFactory->propertyFetch($builderFactory->this(), $builderFactory->id($propertyName)),
                    $builderFactory->methodCall(
                        $builderFactory->propertyFetch($builderFactory->this(), Constants::MAPPER_PROPERTY_NAME),
                        $builderFactory->id(Constants::GET_MAPPER_METHOD_NAME),
                        [$builderFactory->argument($builderFactory->classConstFetch($builderFactory->fullyQualified($class->name), 'class'))]
                    )
                )
            )
        );

        $this->map->put($className, $propertyName);
        return $propertyName;
    }

    public function generateMapperMethod(Type $sourceType, Type $targetType): string
    {
        $sourceReflectionClass = ReflectionClass::forName($sourceType->getClassName());
        $targetReflectionClass = ReflectionClass::forName($targetType->getClassName());

        $methodName = sprintf(
            'from%sTo%s__%s',
            $sourceReflectionClass->getShortName(),
            $targetReflectionClass->getShortName(),
            md5($sourceReflectionClass->name . $targetReflectionClass->name)
        );

        $sourceReflectionProperties = $sourceReflectionClass->getMapperProperties();
        $targetReflectionProperties = $targetReflectionClass->getMapperProperties();

        if (count($sourceReflectionProperties) !== count($targetReflectionProperties)) {
            throw new NotImplementedYet(__METHOD__);
        }

        foreach ($sourceReflectionProperties as $sourceReflectionProperty) {
            $sourcePropertyMatched = false;
            foreach ($targetReflectionProperties as $targetReflectionProperty) {
                if ($sourceReflectionProperty->name === $targetReflectionProperty->name) {
                    $sourcePropertyTypeInfo = $sourceReflectionProperty->getTypeInfo();
                    $targetPropertyTypeInfo = $targetReflectionProperty->getTypeInfo();

                    if (count($sourcePropertyTypeInfo) === 0) {
                        throw new NotImplementedYet(__METHOD__);
                    }

                    if (count($targetPropertyTypeInfo) === 0) {
                        throw new NotImplementedYet(__METHOD__);
                    }

                    if (count($sourcePropertyTypeInfo) > 1) {
                        throw new NotImplementedYet(__METHOD__);
                    }

                    if (count($targetPropertyTypeInfo) > 1) {
                        throw new NotImplementedYet(__METHOD__);
                    }

                    $sourcePropertyType = $sourcePropertyTypeInfo[0];
                    $targetPropertyType = $targetPropertyTypeInfo[0];

                    if (TypeChecker::isSubtype($targetPropertyType, $sourcePropertyType)) {
                        $sourcePropertyMatched = true;
                        break;
                    }
                }
            }

            if (!$sourcePropertyMatched) {
                throw new NotImplementedYet(__METHOD__);
            }
        }

        $builderFactory = new BuilderFactory();

        $varNameProvider = new VarNameProvider();
        $inputVarName = $varNameProvider->getUniqueName('input');

        $method = $this->createMethod($methodName)
            ->makePrivate()
            ->addParam($builderFactory->param($inputVarName)->setType($builderFactory->fullyQualified($sourceReflectionClass->name)))
            ->setReturnType($builderFactory->fullyQualified($targetReflectionClass->name));


        $inputVar = $builderFactory->var($inputVarName);

        // properties to set
        /**
 * @var Map<Variable, ReflectionProperty> $propertiesToSet 
*/
        $map = new Map();
        foreach ($targetReflectionProperties as $targetReflectionProperty) {
            $map->put(
                $builderFactory->var($varNameProvider->getUniqueName($targetReflectionProperty->name)),
                $targetReflectionProperty,
            );
        }

        $extractor = new Extractor();

        // extract source values
        foreach ($map as $propertyVar => $property) {
            $sourceReader = $extractor->getReader($sourceReflectionClass->name, $property->name);
            if (!$sourceReader instanceof Reader) {
                // todo double check
                continue;
            }

            $method->addStmt(
                $builderFactory->assign($propertyVar, $sourceReader->getExpr($inputVar))
            );
        }

        // create object
        $arguments = [];
        $constructor = $targetReflectionClass->getConstructor();
        if ($constructor !== null) {
            $constructorParameters = $constructor->getParameters();
            foreach ($constructorParameters as $constructorParameter) {
                $arguments[] = $builderFactory->argument(
                    $builderFactory->var($constructorParameter->name),
                    $constructorParameter->isPassedByReference(),
                    $constructorParameter->isVariadic(),
                    [],
                    $builderFactory->id($constructorParameter->name)
                );

                foreach ($map as $propertyVar => $property) {
                    if ($property->name === $constructorParameter->name) {
                        $map->remove($propertyVar);
                    }
                }
            }
        }

        $outputVar = $builderFactory->var($varNameProvider->getUniqueName('target'));
        $method->addStmt(
            $builderFactory->assign(
                $outputVar,
                $builderFactory->new($builderFactory->fullyQualified($targetReflectionClass->name), $arguments)
            ),
        );

        foreach ($map as $propertyVar => $property) {
            $writer = $extractor->getWriter($targetReflectionClass->name, $property->name);
            if (!$writer instanceof Writer) {
                // todo double check
                continue;
            }

            $method->addStmt(
                $writer->getExpr($outputVar, $propertyVar)
            );
            $map->remove($propertyVar);
        }

        if (!$map->isEmpty()) {
            throw new RuntimeException(__METHOD__);
        }

        $method->addStmt($builderFactory->return($outputVar));

        return $methodName;
    }
}
