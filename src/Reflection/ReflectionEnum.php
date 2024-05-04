<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Reflection;

use ReflectionException;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use VKPHPUtils\Mapping\Exception\RuntimeException;

/**
 * @psalm-immutable
 */
class ReflectionEnum extends \ReflectionEnum
{

    private readonly NodeFinder $nodeFinder;

    private readonly Parser $parser;

    public function __construct(object|string $objectOrClass)
    {
        parent::__construct($objectOrClass);
        $this->nodeFinder = new NodeFinder();
        $this->parser = (new ParserFactory())->createForHostVersion();
    }

    public static function forName(string $enumClassName): ReflectionEnum
    {
        try {
            return new ReflectionEnum($enumClassName);
        } catch (ReflectionException $reflectionException) {
            throw new RuntimeException($reflectionException->getMessage(), previous: $reflectionException);
        }
    }

    public function isIntBacked(): bool
    {
        if (!$this->isBacked()) {
            return false;
        }

        $stmts = $this->parser->parse(file_get_contents($this->getFileName()));
        $enums = $this->nodeFinder->findInstanceOf($stmts, Enum_::class);
        if (count($enums) !== 1) {
            throw new RuntimeException();
        }

        /**
 * @var Enum_ $enum 
*/
        $enum = $enums[0];

        return $enum->scalarType?->name === 'int';
    }

    public function isStringBacked(): bool
    {
        if (!$this->isBacked()) {
            return false;
        }

        $stmts = $this->parser->parse(file_get_contents($this->getFileName()));
        $enums = $this->nodeFinder->findInstanceOf($stmts, Enum_::class);
        if (count($enums) !== 1) {
            throw new RuntimeException();
        }

        /**
 * @var Enum_ $enum 
*/
        $enum = $enums[0];

        return $enum->scalarType?->name === 'string';
    }
}
