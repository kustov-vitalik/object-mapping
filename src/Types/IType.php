<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types;

interface IType
{
    public function getBuiltinType(): BuiltinType;
}
