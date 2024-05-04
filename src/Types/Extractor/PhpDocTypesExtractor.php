<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types\Extractor;

use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Param as ParamDocBlockTag;
use phpDocumentor\Reflection\DocBlock\Tags\Return_ as ReturnDocBlockTag;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\ContextFactory;
use Symfony\Component\PropertyInfo\Util\PhpDocTypeHelper;
use VKPHPUtils\Mapping\Exception\RuntimeException;

class PhpDocTypesExtractor implements ITypesExtractor
{
    /**
     * @var array<string, DocBlock> 
     */
    private array $docBlocks = [];

    public function getMethodReturnTypes(ReflectionMethod $reflectionMethod): array
    {
        $docBlock = $this->getDocBlock($reflectionMethod);

        /**
 * @var ReturnDocBlockTag $tag 
*/
        foreach ($docBlock->getTagsByName('return') as $tag) {
            if (!$tag instanceof ReturnDocBlockTag) {
                continue;
            }

            if (!$tag->getType() instanceof Type) {
                continue;
            }

            return (new PhpDocTypeHelper())->getTypes($tag->getType());
        }

        throw new RuntimeException(
            sprintf(
                'Unable to extract return type for method "%s::%s". ' .
                'Does it have a valid @return tag in the phpdoc comment?',
                $reflectionMethod->class,
                $reflectionMethod->name
            )
        );
    }

    public function getParameterTypes(ReflectionParameter $reflectionParameter): array
    {
        $reflectionFunctionAbstract = $reflectionParameter->getDeclaringFunction();
        if (!$reflectionFunctionAbstract instanceof ReflectionMethod) {
            throw new RuntimeException();
        }

        $docBlock = $this->getDocBlock($reflectionFunctionAbstract);
        /**
 * @var ParamDocBlockTag $tag 
*/
        foreach ($docBlock->getTagsByName('param') as $tag) {
            if (!$tag instanceof ParamDocBlockTag) {
                continue;
            }

            if ($tag->getVariableName() === null) {
                continue;
            }

            if ($tag->getVariableName() !== $reflectionParameter->name) {
                continue;
            }

            if (!($tag->getType() instanceof Type)) {
                continue;
            }

            return (new PhpDocTypeHelper())->getTypes($tag->getType());
        }

        throw new RuntimeException(
            sprintf(
                'Unable to resolve type for parameter "%s" of the method "%s::%s"',
                $reflectionParameter->name,
                $reflectionParameter->getDeclaringClass()->name,
                $reflectionParameter->getDeclaringFunction()->name
            )
        );
    }

    private function getDocBlock(ReflectionMethod|ReflectionProperty $reflector): DocBlock
    {
        if ($reflector->getDocComment() === false) {
            throw new RuntimeException(
                sprintf(
                    'Unable to get DocBlock from method. The method "%s::%s" does not have a phpdoc comment',
                    $reflector->class,
                    $reflector->name
                )
            );
        }

        $hash = $reflector->class . $reflector->name . $reflector::class;

        return $this->docBlocks[$hash] ?? ($this->docBlocks[$hash] = DocBlockFactory::createInstance()->create(
            $reflector,
            (new ContextFactory())->createFromReflector($reflector)
        ));
    }

    public function getPropertyTypes(ReflectionProperty $reflectionProperty): array
    {
        $docBlock = $this->getDocBlock($reflectionProperty);

        foreach ($docBlock->getTagsWithTypeByName('var') as $tagWithType) {
            if (!$tagWithType instanceof Var_) {
                continue;
            }

            if (!$tagWithType->getType() instanceof Type) {
                continue;
            }

            return (new PhpDocTypeHelper())->getTypes($tagWithType->getType());
        }

        throw new RuntimeException(
            sprintf(
                'Unable to resolve type for property "%s->%s""',
                $reflectionProperty->class,
                $reflectionProperty->name
            )
        );
    }
}
