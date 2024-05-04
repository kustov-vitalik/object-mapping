<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\DependencyInjection\Container;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Dependencies\AddressMapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapperToPersonWithAddress;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromAddress;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonWithAddress;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToAddress;

class MapperToPersonWithAddressTest extends TestCase
{

    public function testMapSuccessPath(): void
    {
        $container = new Container(new AddressMapper());
        $mapper = Mappers::strictTyped($container)->getMapper(MapperToPersonWithAddress::class);
        $fromPersonWithAddress = new FromPersonWithAddress();
        $fromPersonWithAddress->address = new FromAddress();
        $fromPersonWithAddress->firstName = 'fName';
        $toPerson = $mapper->map($fromPersonWithAddress);

        $this->assertNotNull($toPerson);
        $this->assertSame($fromPersonWithAddress->firstName, $toPerson->getFirstName());
        $this->assertInstanceOf(ToAddress::class, $toPerson->getAddress());
        $this->assertNotNull($toPerson->getAddress());
        $this->assertSame($fromPersonWithAddress->address->country, $toPerson->getAddress()->country);
        $this->assertSame($fromPersonWithAddress->address->city, $toPerson->getAddress()->city);
    }

    public function testMapDIContainerShouldBeProvidedException(): void
    {
        $this->expectExceptionObject(new RuntimeException('DI Container should be provided'));
        Mappers::strictTyped()->getMapper(MapperToPersonWithAddress::class);
    }

}
