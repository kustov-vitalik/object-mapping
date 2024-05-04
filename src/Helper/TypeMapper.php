<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Helper;

use ReflectionNamedType;
use ReflectionUnionType;
use ReflectionIntersectionType;
use PhpParser\Node\Name\FullyQualified;
use RuntimeException;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\UnionType;

class TypeMapper
{

    public function mapType(
        ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type
    ): ComplexType|Identifier|Name|string {
        if ($type === null) {
            return new Name('null');
        }

        if ($type->allowsNull()) {
            if ($type instanceof ReflectionUnionType) {
                return new UnionType(array_map($this->mapType(...), [null, ...$type->getTypes()]));
            }

            if ($type instanceof ReflectionNamedType) {
                return new UnionType(
                    [
                    $type->isBuiltin() ? new Name($type->getName()) : new FullyQualified($type->getName()),
                    new Name('null')
                    ]
                );
            }
        }

        if ($type instanceof ReflectionUnionType) {
            return new UnionType(array_map($this->mapType(...), $type->getTypes()));
        }

        if ($type instanceof ReflectionIntersectionType) {
            return new IntersectionType(array_map($this->mapType(...), $type->getTypes()));
        }

        return $type->isBuiltin() ? new Name($type->getName()) : new FullyQualified($type->getName());
    }

}
