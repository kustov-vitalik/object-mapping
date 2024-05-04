<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\DependencyInjection;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $registry = [];

    public function __construct(...$services)
    {
        foreach ($services as $service) {
            $this->register($service);
        }
    }

    public function register(object $service): void
    {
        $this->registry[$service::class] = $service;
    }

    public function get(string $id): object
    {
        return $this->registry[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->registry);
    }
}
