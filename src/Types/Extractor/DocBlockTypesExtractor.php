<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types\Extractor;

use ReflectionClass;
use phpDocumentor\Reflection\Types\ContextFactory;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeParameterNode;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Symfony\Component\PropertyInfo\Type;
use Throwable;
use VKPHPUtils\Mapping\Exception\RuntimeException;

use function count;
use function in_array;

class DocBlockTypesExtractor implements ITypesExtractor
{
    private array $docBlocks = [];

    private readonly PhpDocParser $phpDocParser;

    public function __construct()
    {
        $usesAttributes = ['lines' => true, 'indexes' => true];
        $constExprParser = new ConstExprParser(
            usedAttributes: $usesAttributes
        );
        $typeParser = new TypeParser(
            constExprParser: $constExprParser,
            quoteAwareConstExprString: true,
            usedAttributes: $usesAttributes
        );
        $this->phpDocParser = new PhpDocParser(
            typeParser: $typeParser,
            constantExprParser: $constExprParser,
            usedAttributes: $usesAttributes
        );
    }

    public function getMethodReturnTypes(ReflectionMethod $reflectionMethod): array
    {
        $phpDocNode = $this->getDocBlock($reflectionMethod);

        foreach ($phpDocNode->getReturnTagValues() as $returnTagValueNode) {
            return $this->getTypes(
                $returnTagValueNode,
                $this->createNameResolver($reflectionMethod->class)
            );
        }

        throw new RuntimeException('Unable to extract return type.');
    }

    private function getDocBlock(ReflectionMethod|ReflectionProperty $reflector): PhpDocNode
    {
        $hash = $reflector->class . $reflector->name . $reflector::class;
        if (isset($this->docBlocks[$hash])) {
            return $this->docBlocks[$hash];
        }

        if ($reflector->getDocComment() === false) {
            throw new RuntimeException(
                sprintf(
                    'Unable to get DocBlock from method. The method "%s::%s" does not have a phpdoc comment',
                    $reflector->class,
                    $reflector->name
                )
            );
        }

        try {
            $tokens = new TokenIterator((new Lexer())->tokenize($reflector->getDocComment()));
            $phpDocNode = $this->phpDocParser->parse($tokens);
            $tokens->consumeTokenType(Lexer::TOKEN_END);
            return $this->docBlocks[$hash] = $phpDocNode;
        } catch (Throwable $throwable) {
            throw new RuntimeException($throwable->getMessage(), previous: $throwable);
        }
    }

    private function getTypes(PhpDocTagValueNode $phpDocTagValueNode, ClassNameResolver $classNameResolver): array
    {
        if ($phpDocTagValueNode instanceof ParamTagValueNode
            || $phpDocTagValueNode instanceof ReturnTagValueNode
            || $phpDocTagValueNode instanceof VarTagValueNode
        ) {
            return $this->compressNullableType($this->extractTypes($phpDocTagValueNode->type, $classNameResolver));
        }

        return [];
    }

    /**
     * @param  Type[] $types
     * @return Type[]
     */
    private function compressNullableType(array $types): array
    {
        $firstTypeIndex = null;
        $nullableTypeIndex = null;

        foreach ($types as $k => $type) {
            if (null === $firstTypeIndex
                && Type::BUILTIN_TYPE_NULL !== $type->getBuiltinType()
                && !$type->isNullable()
            ) {
                $firstTypeIndex = $k;
            }

            if (null === $nullableTypeIndex && Type::BUILTIN_TYPE_NULL === $type->getBuiltinType()) {
                $nullableTypeIndex = $k;
            }

            if (null === $firstTypeIndex) {
                continue;
            }

            if (null === $nullableTypeIndex) {
                continue;
            }

            break;
        }

        if (null !== $firstTypeIndex && null !== $nullableTypeIndex) {
            $firstType = $types[$firstTypeIndex];
            $types[$firstTypeIndex] = new Type(
                $firstType->getBuiltinType(),
                true,
                $firstType->getClassName(),
                $firstType->isCollection(),
                $firstType->getCollectionKeyTypes(),
                $firstType->getCollectionValueTypes()
            );
            unset($types[$nullableTypeIndex]);
        }

        return array_values($types);
    }

    /**
     * @return Type[]
     */
    private function extractTypes(TypeNode $typeNode, ClassNameResolver $classNameResolver): array
    {
        if ($typeNode instanceof UnionTypeNode) {
            $types = [];
            foreach ($typeNode->types as $type) {
                if ($type instanceof ConstTypeNode) {
                    // It's safer to fall back to other extractors here, as resolving const types correctly is not easy at the moment
                    return [];
                }

                foreach ($this->extractTypes($type, $classNameResolver) as $subType) {
                    $types[] = $subType;
                }
            }

            return $this->compressNullableType($types);
        }

        if ($typeNode instanceof GenericTypeNode) {
            if ('class-string' === $typeNode->type->name) {
                return [new Type(Type::BUILTIN_TYPE_STRING)];
            }

            [$mainType] = $this->extractTypes($typeNode->type, $classNameResolver);

            if (Type::BUILTIN_TYPE_INT === $mainType->getBuiltinType()) {
                return [$mainType];
            }

            $collectionKeyTypes = $mainType->getCollectionKeyTypes();
            $collectionKeyValues = [];
            if (1 === count($typeNode->genericTypes)) {
                foreach ($this->extractTypes($typeNode->genericTypes[0], $classNameResolver) as $subType) {
                    $collectionKeyValues[] = $subType;
                }
            } elseif (2 === count($typeNode->genericTypes)) {
                foreach ($this->extractTypes($typeNode->genericTypes[0], $classNameResolver) as $keySubType) {
                    $collectionKeyTypes[] = $keySubType;
                }

                foreach ($this->extractTypes($typeNode->genericTypes[1], $classNameResolver) as $valueSubType) {
                    $collectionKeyValues[] = $valueSubType;
                }
            }

            return [
                new Type(
                    $mainType->getBuiltinType(),
                    $mainType->isNullable(),
                    $mainType->getClassName(),
                    true,
                    $collectionKeyTypes,
                    $collectionKeyValues
                )
            ];
        }

        if ($typeNode instanceof ArrayShapeNode) {
            return [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true)];
        }

        if ($typeNode instanceof ArrayTypeNode) {
            return [
                new Type(
                    Type::BUILTIN_TYPE_ARRAY,
                    false,
                    null,
                    true,
                    [new Type(Type::BUILTIN_TYPE_INT)],
                    $this->extractTypes($typeNode->type, $classNameResolver)
                )
            ];
        }

        if ($typeNode instanceof CallableTypeNode || $typeNode instanceof CallableTypeParameterNode) {
            return [new Type(Type::BUILTIN_TYPE_CALLABLE)];
        }

        if ($typeNode instanceof NullableTypeNode) {
            $subTypes = $this->extractTypes($typeNode->type, $classNameResolver);
            if (count($subTypes) > 1) {
                $subTypes[] = new Type(Type::BUILTIN_TYPE_NULL);

                return $subTypes;
            }

            return [
                new Type(
                    $subTypes[0]->getBuiltinType(),
                    true,
                    $subTypes[0]->getClassName(),
                    $subTypes[0]->isCollection(),
                    $subTypes[0]->getCollectionKeyTypes(),
                    $subTypes[0]->getCollectionValueTypes()
                )
            ];
        }

        if ($typeNode instanceof ThisTypeNode) {
            return [new Type(Type::BUILTIN_TYPE_OBJECT, false, $classNameResolver->resolveRootClass())];
        }

        if ($typeNode instanceof IdentifierTypeNode) {
            if (in_array($typeNode->name, Type::$builtinTypes)) {
                return [new Type($typeNode->name, false, null, in_array($typeNode->name, Type::$builtinCollectionTypes))];
            }

            return match ($typeNode->name) {
                'integer',
                'positive-int',
                'negative-int' => [new Type(Type::BUILTIN_TYPE_INT)],
                'double' => [new Type(Type::BUILTIN_TYPE_FLOAT)],
                'list',
                'non-empty-list' => [
                    new Type(
                        builtinType: Type::BUILTIN_TYPE_ARRAY,
                        nullable: false,
                        class: null,
                        collection: true,
                        collectionKeyType: new Type(Type::BUILTIN_TYPE_INT)
                    )
                ],
                'non-empty-array' => [new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true)],
                'mixed' => [], // mixed seems to be ignored in all other extractors
                'parent' => [new Type(Type::BUILTIN_TYPE_OBJECT, false, $typeNode->name)],
                'static',
                'self' => [new Type(Type::BUILTIN_TYPE_OBJECT, false, $classNameResolver->resolveRootClass())],
                'class-string',
                'html-escaped-string',
                'lowercase-string',
                'non-empty-lowercase-string',
                'non-empty-string',
                'numeric-string',
                'trait-string',
                'interface-string',
                'literal-string' => [new Type(Type::BUILTIN_TYPE_STRING)],
                'void' => [new Type(Type::BUILTIN_TYPE_NULL)],
                'scalar' => [
                    new Type(Type::BUILTIN_TYPE_INT),
                    new Type(Type::BUILTIN_TYPE_FLOAT),
                    new Type(Type::BUILTIN_TYPE_STRING),
                    new Type(Type::BUILTIN_TYPE_BOOL)
                ],
                'number' => [new Type(Type::BUILTIN_TYPE_INT), new Type(Type::BUILTIN_TYPE_FLOAT)],
                'numeric' => [
                    new Type(Type::BUILTIN_TYPE_INT),
                    new Type(Type::BUILTIN_TYPE_FLOAT),
                    new Type(Type::BUILTIN_TYPE_STRING)
                ],
                'array-key' => [new Type(Type::BUILTIN_TYPE_STRING), new Type(Type::BUILTIN_TYPE_INT)],
                default => [
                    new Type(
                        Type::BUILTIN_TYPE_OBJECT,
                        false,
                        $classNameResolver->resolveStringName($typeNode->name)
                    )
                ],
            };
        }

        return [];
    }

    private function createNameResolver(string $calledClassName, string $declaringClassName = null): ClassNameResolver
    {
        $declaringClassName ??= $calledClassName;

        $path = explode('\\', $calledClassName);
        $calledClassShortName = array_pop($path);

        $reflectionClass = new ReflectionClass($declaringClassName);
        [$declaringNamespace, $declaringUses] = $this->extractFromFullClassName($reflectionClass);
        $declaringUses = array_merge($declaringUses, $this->collectUses($reflectionClass));

        return new ClassNameResolver($calledClassShortName, $declaringNamespace, $declaringUses);
    }

    private function extractFromFullClassName(ReflectionClass $reflectionClass): array
    {
        $namespace = trim($reflectionClass->getNamespaceName(), '\\');
        $fileName = $reflectionClass->getFileName();

        if (\is_string($fileName) && is_file($fileName)) {
            $contents = file_get_contents($fileName);
            if (false === $contents) {
                throw new \RuntimeException(sprintf('Unable to read file "%s".', $fileName));
            }

            return [
                $namespace,
                (new ContextFactory())->createForNamespace($namespace, $contents)->getNamespaceAliases()
            ];
        }

        return [$namespace, []];
    }

    private function collectUses(ReflectionClass $reflectionClass): array
    {
        $uses = [$this->extractFromFullClassName($reflectionClass)[1]];

        foreach ($reflectionClass->getTraits() as $traitReflection) {
            $uses[] = $this->extractFromFullClassName($traitReflection)[1];
        }

        $parentClass = $reflectionClass->getParentClass();
        if ($parentClass instanceof ReflectionClass) {
            $uses[] = $this->collectUses($parentClass);
        }

        return $uses !== [] ? array_merge(...$uses) : [];
    }

    public function getParameterTypes(ReflectionParameter $reflectionParameter): array
    {
        $reflectionFunctionAbstract = $reflectionParameter->getDeclaringFunction();
        if (!$reflectionFunctionAbstract instanceof ReflectionMethod) {
            throw new RuntimeException(__METHOD__ . ' is only valid for methods.');
        }

        $phpDocNode = $this->getDocBlock($reflectionFunctionAbstract);


        foreach ($phpDocNode->getParamTagValues() as $paramTagValueNode) {
            if ($paramTagValueNode->parameterName === '$' . $reflectionParameter->name) {
                return $this->getTypes(
                    $paramTagValueNode,
                    $this->createNameResolver($reflectionParameter->getDeclaringClass()?->name)
                );
            }
        }

        throw new RuntimeException('Unable to extract parameter types.');
    }

    public function getPropertyTypes(ReflectionProperty $reflectionProperty): array
    {
        $phpDocNode = $this->getDocBlock($reflectionProperty);

        foreach ($phpDocNode->getVarTagValues() as $varTagValueNode) {
            if ($varTagValueNode->variableName === '$' . $reflectionProperty->name) {
                return $this->getTypes(
                    $varTagValueNode,
                    $this->createNameResolver($reflectionProperty->class)
                );
            }
        }

        throw new RuntimeException('Unable to extract property types.');
    }

}
