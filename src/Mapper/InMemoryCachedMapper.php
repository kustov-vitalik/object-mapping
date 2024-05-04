<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Mapper;

use VKPHPUtils\Mapping\IMapper;

class InMemoryCachedMapper implements IMapper
{
    private array $registry = [];

    public function __construct(private readonly IMapper $mapper)
    {
    }


    public function getMapper(string $className): object
    {
        return $this->registry[$className] ?? ($this->registry[$className] = $this->mapper->getMapper($className));
    }
}
