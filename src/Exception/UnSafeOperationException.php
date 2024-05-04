<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Exception;

use Symfony\Component\PropertyInfo\Type;

class UnSafeOperationException extends RuntimeException
{
    public function __construct(Type $from, Type $to)
    {
        parent::__construct(
            sprintf("'%s' is not safe to convert to '%s'", $from->getBuiltinType(), $to->getBuiltinType())
        );
    }
}
