<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From;

use DateTimeInterface;
use DateTimeImmutable;
class FromVehicle
{
    public function __construct(
        private readonly string $model = 'Toyota',
        public readonly DateTimeInterface $manufacturedAt = new DateTimeImmutable(),
        private FromDriver $driver = new FromDriver(),
    ) {
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return FromDriver
     */
    public function getDriver(): FromDriver
    {
        return $this->driver;
    }

    public function setDriver(FromDriver $driver): void
    {
        $this->driver = $driver;
    }
}
