<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator;

interface IClassNameGenerator
{
    /**
     * @return class-string
     */
    public function generateMapperClassName(string $mapperClassName): string;
}
