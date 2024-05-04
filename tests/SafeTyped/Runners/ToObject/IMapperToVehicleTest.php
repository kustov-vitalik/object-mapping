<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToObject;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToObject\IMapperToVehicle;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromVehicle;

class IMapperToVehicleTest extends TestCase
{
    #[Test]
    public function fromVehicle(): void
    {
        $mapper = Mappers::safeTyped()->getMapper(IMapperToVehicle::class);

        $toVehicle = $mapper->fromVehicle($fromVehicle = new FromVehicle());

        $this->assertNotNull($toVehicle);
    }
}
