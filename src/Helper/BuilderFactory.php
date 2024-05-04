<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Helper;

use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;

class BuilderFactory extends \PhpParser\BuilderFactory
{
    public function appendToArray(Expr $arrayVar, Expr $value): Assign
    {
        return $this->assign(
            new ArrayDimFetch($arrayVar, null),
            $value
        );
    }

    public function assign(Expr $var, Expr $expr, array $attributes = []): Assign
    {
        return new Assign(
            $var,
            $expr,
            $attributes
        );
    }

    public function this(): Variable
    {
        return $this->var('this');
    }

    public function return(Expr|null $expr = null, array $attributes = []): Return_
    {
        return new Return_($expr, $attributes);
    }

    public function id(string $name, array $attributes = []): Identifier
    {
        return new Identifier($name, $attributes);
    }

    public function expression(Expr $expr, array $attributes = []): Expression
    {
        return new Expression($expr, $attributes);
    }

    /**
     * @param Stmt[]     $bodyStmts
     * @param ElseIf_[]  $elseif
     */
    public function if(
        Expr $expr,
        array $bodyStmts = [],
        array $elseif = [],
        Else_|null $else = null,
        array $attributes = []
    ): If_ {
        return new If_($expr, ['stmts' => $bodyStmts, 'elseifs' => $elseif, 'else' => $else], $attributes);
    }

    public function instanceOf(Expr $expr, Expr|Name $class, array $attributes = []): Instanceof_
    {
        return new Instanceof_($expr, $class, $attributes);
    }

    public function not(Expr $expr, array $attributes = []): BooleanNot
    {
        return new BooleanNot($expr, $attributes);
    }

    public function throwNewException(Expr|Name|string $className, string $message): Throw_
    {
        return new Throw_($this->new($this->fullyQualified($className), [$this->val($message)]));
    }

    /**
     * Constructs a Name node.
     *
     * @param Name|string|string[] $name       - Name as string, part array or Name instance (copy ctor).
     * @param array                $attributes - Additional attributes.
     */
    public function name(Name|string|array $name, array $attributes = []): Name
    {
        return new Name($name, $attributes);
    }

    /**
     * Constructs a Name node.
     *
     * @param Name|string|string[] $name       - Name as string, part array or Name instance (copy ctor).
     * @param array                $attributes - Additional attributes.
     */
    public function fullyQualified(Name|string|array $name, array $attributes = []): FullyQualified
    {
        return new FullyQualified($name, $attributes);
    }

    public function argument(
        Expr $expr,
        bool $byRef = false,
        bool $unpack = false,
        array $attributes = [],
        Identifier|null $name = null
    ): Arg {
        return new Arg(
            $expr,
            $byRef,
            $unpack,
            $attributes,
            $name
        );
    }

    /**
     * @return Arg[]
     */
    public function arguments(Arg...$arg): array
    {
        return $this->args($arg);
    }

    public function assignByRef(Expr $var, Expr $expr, array $attributes = []): AssignRef
    {
        return new AssignRef(
            $var,
            $expr,
            $attributes
        );
    }

    /**
     * @param ArrayItem[] $items
     */
    public function array(array $items = [], array $attributes = []): Array_
    {
        return new Array_($items, $attributes);
    }

    public function coalesce(Expr $left, Expr $right): Coalesce
    {
        return new Coalesce($left, $right);
    }

    public function arrayDimFetch(Expr $var, Expr $dim): ArrayDimFetch
    {
        return new ArrayDimFetch($var, $dim);
    }
}
