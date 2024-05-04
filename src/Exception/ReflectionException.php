<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Exception;

class ReflectionException extends RuntimeException
{
    /**
     * @param \ReflectionException $reflectionException
     */
    public static function fromReflectionException(\ReflectionException $reflectionException): ReflectionException
    {
        return new ReflectionException($reflectionException->getMessage(), $reflectionException->getCode(), $reflectionException);
    }
}
