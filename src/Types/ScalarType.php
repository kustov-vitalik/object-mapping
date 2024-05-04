<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types;

use InvalidArgumentException;
abstract class ScalarType implements IType
{
    public function __construct(protected BuiltinType $builtinType)
    {
        if (!$this->builtinType->isScalar()) {
            throw new InvalidArgumentException(sprintf("Unknown scalar type '%s'", $builtinType->name));
        }
    }

    public function getBuiltinType(): BuiltinType
    {
        return $this->builtinType;
    }
}
