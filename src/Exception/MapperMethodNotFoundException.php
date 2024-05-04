<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Exception;

use Symfony\Component\PropertyInfo\Type;

class MapperMethodNotFoundException extends RuntimeException
{
    public function __construct(public readonly Type $sourceType, public readonly Type $targetType, string $message = "", int $code = 0, ?Throwable $throwable = null)
    {
        parent::__construct($message, $code, $throwable);
    }
}
