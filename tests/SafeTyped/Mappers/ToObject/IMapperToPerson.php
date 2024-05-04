<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToObject;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\ArrayAccessObject;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPartialPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\To\ToPerson;

#[Mapper]
interface IMapperToPerson
{
    public function fromArray(array $person): ToPerson;

    public function fromArrayAccessObject(ArrayAccessObject $arrayAccessObject): ToPerson;

    public function fromArrayAndPerson(array $person1, FromPerson $fromPerson): ToPerson;

    public function fromPersonAndArray(FromPerson $fromPerson, array $person2): ToPerson;

    public function fromPartialPersonAndArray(FromPartialPerson $fromPartialPerson, array $person2): ToPerson;

    public function fromPartialPersonAndScalar(FromPartialPerson $fromPartialPerson, int $age): ToPerson;

    public function fromScalarAndPartialPerson(int $age, FromPartialPerson $fromPartialPerson): ToPerson;

    public function fromScalarsAndPartialPerson(int $age, string $lastName, FromPartialPerson $fromPartialPerson): ToPerson;

    public function fromArrayAndObject(array $person1, object $person2): ToPerson;

    public function fromArrays(array $person1, array $person2): ToPerson;

    public function fromScalars(string $firstName, string $lastName, int $age): ToPerson;

    public function fromScalarsShuffled(string $lastName, int $age, string $firstName): ToPerson;

    public function fromArrayAndScalarAge(array $person, int $age): ToPerson;

    public function fromArrayAndScalarsAgeAndFirstName(array $person, int $age, string $firstName): ToPerson;

    public function fromArrayAndScalarsFirstNameAndAge(array $person, string $firstName, int $age): ToPerson;

    public function fromArrayAndScalarsAgeAndLastName(array $person, int $age, string $lastName): ToPerson;

    public function fromArrayAndScalarsLastNameAndAge(array $person, string $lastName, int $age): ToPerson;

    public function fromPerson(FromPerson $fromPerson): ToPerson;

    public function fromObject(object $person): ToPerson;
}
