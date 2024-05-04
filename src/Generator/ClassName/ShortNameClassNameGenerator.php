<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\ClassName;

use VKPHPUtils\Mapping\Generator\IClassNameGenerator;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;

final class ShortNameClassNameGenerator implements IClassNameGenerator
{
    public function generateMapperClassName(string $mapperClassName): string
    {
        $reflectionClass = ReflectionClass::forName($mapperClassName);

        return $reflectionClass->getShortName() . '_' . md5($reflectionClass->name . self::class);
    }
}
