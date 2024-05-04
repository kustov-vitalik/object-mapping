<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToObject;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Mapping;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromDriver;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromFullName;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromVehicle;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToDriver;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToModelName;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToVehicle;

#[Mapper]
abstract class IMapperToVehicle
{
    #[Mapping(target: 'modelName', source: 'model', qualifier: new Qualifier('toModelName'))]
    #[Mapping(target: 'driver', source: 'driver', qualifier: new Qualifier('fromDriver'))]
    abstract public function fromVehicle(FromVehicle $fromVehicle): ToVehicle;

    public function toModelName(string $name): ToModelName {
        return new ToModelName($name);
    }

    #[Mapping(target: 'name', source: 'fullName', qualifier: new Qualifier('fromDriverFullName'))]
    #[Mapping(target: 'fatherName', source: 'father', qualifier: new Qualifier('extractFatherName'))]
    abstract public function fromDriver(FromDriver $driver): ToDriver;


    #[Mapping(target: '', source: 'value')]
    abstract public function fromDriverFullName(FromFullName $fromFullName): string;

    public function extractFatherName(FromPerson $driverFather): string {
        return $driverFather->firstName . ' ' . $driverFather->lastName;
    }
}
