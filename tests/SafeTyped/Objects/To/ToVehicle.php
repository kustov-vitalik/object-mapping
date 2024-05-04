<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To;

use DateTimeInterface;
class ToVehicle
{
    private DateTimeInterface $manufacturedAt;

    public function __construct(
        private readonly ToModelName $modelName,
        private readonly ToDriver $driver,
    ) {
    }

    /**
     * @return ToDriver
     */
    public function getDriver(): ToDriver
    {
        return $this->driver;
    }

    /**
     * @return ToModelName
     */
    public function getModelName(): ToModelName
    {
        return $this->modelName;
    }

    /**
     * @return DateTimeInterface
     */
    public function getManufacturedAt(): DateTimeInterface
    {
        return $this->manufacturedAt;
    }

    public function setManufacturedAt(DateTimeInterface $manufacturedAt): void
    {
        $this->manufacturedAt = $manufacturedAt;
    }
}
