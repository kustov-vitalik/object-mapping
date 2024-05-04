<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping;

interface IMapper
{
    /**
     * @psalm-template T
     * @psalm-param    class-string<T> $className
     * @return         T
     */
    public function getMapper(string $className): object;
}
