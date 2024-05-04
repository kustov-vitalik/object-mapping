<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Helper;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

final readonly class ClassASTHolder
{
    /**
     * @param Stmt[] $stmts
     */
    private function __construct(
        private array $stmts,
        private NodeFinder $nodeFinder = new NodeFinder(),
    ) {
    }

    public static function forClass(ReflectionClass $reflectionClass): ClassASTHolder
    {
        if (!$reflectionClass->isUserDefined()) {
            throw new InvalidArgumentException(sprintf("ReflectionClass '%s' is not user-defined", $reflectionClass->name));
        }

        $stmts = (new ParserFactory())->createForHostVersion()
            ->parse(file_get_contents($reflectionClass->getFileName())) ?? [];
        return new ClassASTHolder(stmts: $stmts);
    }

    public static function forStmt(Class_ $class): ClassASTHolder
    {
        return new ClassASTHolder(stmts: $class->stmts);
    }

    public function findConstructorAST(): ClassMethod|null
    {
        $constructor = $this->findFirst(static fn(Node $node): bool => ($node instanceof ClassMethod) && $node->name->toString() === '__construct');

        if ($constructor instanceof ClassMethod) {
            return $constructor;
        }

        return null;
    }

    /**
     * @param callable(Node):bool $filter
     * @param Stmt[]              $stmts
     */
    private function findFirst(callable $filter, array $stmts = []): Node|null
    {
        return $this->nodeFinder->findFirst($stmts === [] ? $this->stmts : $stmts, $filter);
    }

    public function findPublicGetterMethodAST(ReflectionProperty $reflectionProperty): ClassMethod|null
    {
        $classMethods = $this->findInstanceOf(ClassMethod::class);
        $return = new Return_(new PropertyFetch(new Variable('this'), $reflectionProperty->name));

        $candidates = [];
        foreach ($classMethods as $classMethod) {
            $returnStmt = $this->findFirstInstanceOf(Return_::class, $classMethod->stmts ?? []);
            if ($returnStmt == $return) {
                $candidates[] = $classMethod;
            }
        }

        if ($candidates === []) {
            return null;
        }

        if (count($candidates) > 1) {
            throw new NotImplementedYet();
        }

        return reset($candidates);
    }

    /**
     * @template T of Node
     * @param    class-string<T> $class
     * @param    Stmt[]          $stmts
     * @return   T[]
     */
    private function findInstanceOf(string $class, array $stmts = []): array
    {
        return $this->nodeFinder->findInstanceOf($stmts === [] ? $this->stmts : $stmts, $class);
    }

    /**
     * @template T of Node
     * @param    class-string<T> $class
     * @param    Stmt[]          $stmts
     * @return   T|null
     */
    private function findFirstInstanceOf(string $class, array $stmts = []): Node|null
    {
        return $this->nodeFinder->findFirstInstanceOf($stmts === [] ? $this->stmts : $stmts, $class);
    }

    /**
     * Finds the only public method that contains expression <code>$this->propertyName = ...</code>
     */
    public function findPublicSetterMethodAST(ReflectionProperty $reflectionProperty): ClassMethod|null
    {
        $classMethods = $this->findInstanceOf(ClassMethod::class);
        $candidates = [];
        foreach ($classMethods as $classMethod) {
            if ($classMethod->name->toString() === '__construct') {
                continue;
            }

            if (!$classMethod->isPublic()) {
                continue;
            }

            $assignExpressions = $this->findInstanceOf(Assign::class, $classMethod->stmts ?? []);

            $currentPropertyFetch = new PropertyFetch(new Variable('this'), $reflectionProperty->name);
            foreach ($assignExpressions as $assignExpression) {
                if ($assignExpression->var == $currentPropertyFetch) {
                    $candidates[] = $classMethod;
                    break;
                }
            }
        }

        if ($candidates === []) {
            return null;
        }

        if (count($candidates) > 1) {
            throw new NotImplementedYet();
        }

        return reset($candidates);
    }

    public function findPropertyAST(ReflectionProperty $reflectionProperty): Property|null
    {
        $propertyASTs = $this->findInstanceOf(Property::class);

        $candidatePropertyASTs = [];
        foreach ($propertyASTs as $propertyAST) {
            $checkPublic = $propertyAST->isPublic() === $reflectionProperty->isPublic();
            $checkProtected = $propertyAST->isProtected() === $reflectionProperty->isProtected();
            $checkPrivate = $propertyAST->isPrivate() === $reflectionProperty->isPrivate();
            $checkReadonly = $propertyAST->isReadonly() === $reflectionProperty->isReadOnly();
            $checkStatic = $propertyAST->isStatic() === $reflectionProperty->isStatic();

            if (!($checkPublic && $checkProtected && $checkPrivate && $checkReadonly && $checkStatic)) {
                continue;
            }

            foreach ($propertyAST->props as $prop) {
                if ($prop->name->name === $reflectionProperty->name) {
                    $candidatePropertyASTs[] = $propertyAST;
                    break;
                }
            }
        }

        if ($candidatePropertyASTs === []) {
            return null;
        }

        if (count($candidatePropertyASTs) > 1) {
            throw new NotImplementedYet();
        }

        return reset($candidatePropertyASTs);
    }

}
