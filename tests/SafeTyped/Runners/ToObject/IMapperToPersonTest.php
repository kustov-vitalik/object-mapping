<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Runners\ToObject;

use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\BeforeClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\IMapper;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\SafeTyped\Mappers\ToObject\IMapperToPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\ArrayAccessObject;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPartialPerson;
use VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From\FromPerson;

class IMapperToPersonTest extends TestCase
{
    private array $personDict = ['firstName' => 'Leonard', 'lastName' => 'Euler', 'age' => 55];

    private FromPerson $fromPerson;

    private object $personObject;

    private static IMapper $mapper;

    #[BeforeClass]
    public static function beforeAll(): void
    {
        self::$mapper = Mappers::safeTyped();
    }

    #[Before]
    public function beforeEach(): void
    {
        $this->fromPerson = new FromPerson(
            $this->personDict['firstName'],
            $this->personDict['lastName'],
            $this->personDict['age']
        );

        $this->personObject = (object)$this->personDict;


    }

    #[Test]
    public function fromArray(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArray($this->personDict);

        $this->assertNotNull($to);
        $this->assertEquals($this->personDict['firstName'], $to->firstName);
        $this->assertEquals($this->personDict['lastName'], $to->lastName);
        $this->assertEquals($this->personDict['age'], $to->age);
    }

    #[Test]
    public function fromArrayAccessObject(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $arrayAccessObject = new ArrayAccessObject();
        $arrayAccessObject['firstName'] = $this->personDict['firstName'];
        $arrayAccessObject['lastName'] = $this->personDict['lastName'];
        $arrayAccessObject['age'] = $this->personDict['age'];
        $to = $mapper->fromArrayAccessObject($arrayAccessObject);

        $this->assertNotNull($to);
        $this->assertEquals($arrayAccessObject['firstName'], $to->firstName);
        $this->assertEquals($arrayAccessObject['lastName'], $to->lastName);
        $this->assertEquals($arrayAccessObject['age'], $to->age);
    }

    #[Test]
    public function fromArrayAndPerson(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArrayAndPerson($from1 = ['firstName' => 'some'], $this->fromPerson);

        $this->assertNotNull($to);
        $this->assertSame($from1['firstName'], $to->firstName);
        $this->assertSame($this->fromPerson->lastName, $to->lastName);
        $this->assertEqualsWithDelta($this->fromPerson->age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromPersonAndArray(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromPersonAndArray($this->fromPerson, $from1 = ['firstName' => 'some']);

        // from1 ignored because FromPerson has all the necessary fields
        $this->assertNotNull($to);
        $this->assertSame($this->fromPerson->firstName, $to->firstName);
        $this->assertSame($this->fromPerson->lastName, $to->lastName);
        $this->assertEqualsWithDelta($this->fromPerson->age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromPartialPersonAndArray(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromPartialPersonAndArray(
            $fromPartialPerson = new FromPartialPerson($this->fromPerson->firstName, $this->fromPerson->lastName),
            $this->personDict
        );

        // personDict[firstName] and personDict[lastName] ignored bc partialPerson has the fields
        $this->assertNotNull($to);
        $this->assertSame($fromPartialPerson->firstName, $to->firstName);
        $this->assertSame($fromPartialPerson->lastName, $to->lastName);
        $this->assertEquals($this->personDict['age'], $to->age);
    }

    #[Test]
    public function fromPartialPersonAndScalar(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromPartialPersonAndScalar(
            $fromPartialPerson = new FromPartialPerson($this->fromPerson->firstName, $this->fromPerson->lastName),
            $age = 34
        );

        $this->assertNotNull($to);
        $this->assertSame($fromPartialPerson->firstName, $to->firstName);
        $this->assertSame($fromPartialPerson->lastName, $to->lastName);
        $this->assertEqualsWithDelta($age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromScalarAndPartialPerson(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromScalarAndPartialPerson(
            $age = 34,
            $fromPartialPerson = new FromPartialPerson($this->fromPerson->firstName, $this->fromPerson->lastName),
        );

        $this->assertNotNull($to);
        $this->assertSame($fromPartialPerson->firstName, $to->firstName);
        $this->assertSame($fromPartialPerson->lastName, $to->lastName);
        $this->assertEqualsWithDelta($age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromScalarsAndPartialPerson(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromScalarsAndPartialPerson(
            $age = 34,
            $lastName = 'someLName',
            $fromPartialPerson = new FromPartialPerson($this->fromPerson->firstName, $this->fromPerson->lastName),
        );

        $this->assertNotNull($to);
        $this->assertSame($fromPartialPerson->firstName, $to->firstName);
        $this->assertSame($lastName, $to->lastName);
        $this->assertEqualsWithDelta($age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromArrayAndObject(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArrayAndObject($from1 = ['firstName' => 'some'], $this->personObject);

        $this->assertNotNull($to);
        $this->assertSame($from1['firstName'], $to->firstName);
        $this->assertEquals($this->personObject->lastName, $to->lastName);
        $this->assertEquals($this->personObject->age, $to->age);
    }

    #[Test]
    public function fromArrays(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArrays(
            $from1 = ['firstName' => 'John', 'age' => 22],
            $from2 = ['lastName' => 'Smith', 'age' => 33]
        );

        $this->assertNotNull($to);
        $this->assertSame($from1['firstName'], $to->firstName);
        $this->assertSame($from2['lastName'], $to->lastName);

        // todo make a convention from1[age] or from2[age]
        $this->assertEqualsWithDelta($from1['age'], $to->age, PHP_FLOAT_EPSILON);
    }


    #[Test]
    public function fromScalars(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromScalars($firstName = 'fName', $lastName = 'lName', $age = 12);

        $this->assertNotNull($to);
        $this->assertSame($firstName, $to->firstName);
        $this->assertSame($lastName, $to->lastName);
        $this->assertEqualsWithDelta($age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromScalarsShuffled(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromScalarsShuffled($lastName = 'lName', $age = 12, $firstName = 'fName');

        $this->assertNotNull($to);
        $this->assertSame($firstName, $to->firstName);
        $this->assertSame($lastName, $to->lastName);
        $this->assertEqualsWithDelta($age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromArrayAndScalarAge(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArrayAndScalarAge($this->personDict, $age = 14);

        $this->assertNotNull($to);
        $this->assertEquals($this->personDict['firstName'], $to->firstName);
        $this->assertEquals($this->personDict['lastName'], $to->lastName);
        $this->assertEquals($this->personDict['age'], $to->age);
    }

    #[Test]
    public function fromArrayAndScalarsAgeAndFirstName(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArrayAndScalarsAgeAndFirstName($this->personDict, $age = 14, $firstName = 'some');

        $this->assertNotNull($to);
        $this->assertEquals($this->personDict['firstName'], $to->firstName);
        $this->assertEquals($this->personDict['lastName'], $to->lastName);
        $this->assertEquals($this->personDict['age'], $to->age);


        $to = $mapper->fromArrayAndScalarsAgeAndFirstName($from = ['lastName' => 'someLName'], $age = 14, $firstName = 'some');

        $this->assertNotNull($to);
        $this->assertSame($firstName, $to->firstName);
        $this->assertSame($from['lastName'], $to->lastName);
        $this->assertEqualsWithDelta($age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromArrayAndScalarsFirstNameAndAge(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArrayAndScalarsFirstNameAndAge($this->personDict, $firstName = 'some', $age = 14);

        $this->assertNotNull($to);
        $this->assertEquals($this->personDict['firstName'], $to->firstName);
        $this->assertEquals($this->personDict['lastName'], $to->lastName);
        $this->assertEquals($this->personDict['age'], $to->age);

        $to = $mapper->fromArrayAndScalarsFirstNameAndAge($from = ['lastName' => 'fName'], $firstName = 'some', $age = 14);

        $this->assertNotNull($to);
        $this->assertSame($from['lastName'], $to->lastName);
        $this->assertSame($firstName, $to->firstName);
        $this->assertEqualsWithDelta($age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromArrayAndScalarsAgeAndLastName(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArrayAndScalarsAgeAndLastName($this->personDict, $age = 14, $lastName = 'some');

        $this->assertNotNull($to);
        $this->assertEquals($this->personDict['firstName'], $to->firstName);
        $this->assertEquals($this->personDict['lastName'], $to->lastName);
        $this->assertEquals($this->personDict['age'], $to->age);


        $to = $mapper->fromArrayAndScalarsAgeAndLastName($from = ['firstName' => 'someFName'], $age = 14, $lastName = 'some');

        $this->assertNotNull($to);
        $this->assertSame($from['firstName'], $to->firstName);
        $this->assertSame($lastName, $to->lastName);
        $this->assertEqualsWithDelta($age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromArrayAndScalarsLastNameAndAge(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromArrayAndScalarsLastNameAndAge($this->personDict, $lastName = 'some', $age = 14);

        $this->assertNotNull($to);
        $this->assertEquals($this->personDict['firstName'], $to->firstName);
        $this->assertEquals($this->personDict['lastName'], $to->lastName);
        $this->assertEquals($this->personDict['age'], $to->age);

        $to = $mapper->fromArrayAndScalarsLastNameAndAge($from = ['firstName' => 'someFirstName'], $lastName = 'some', $age = 14);

        $this->assertNotNull($to);
        $this->assertSame($from['firstName'], $to->firstName);
        $this->assertSame($lastName, $to->lastName);
        $this->assertEqualsWithDelta($age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromPerson(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromPerson($this->fromPerson);

        $this->assertNotNull($to);
        $this->assertSame($this->fromPerson->firstName, $to->firstName);
        $this->assertSame($this->fromPerson->lastName, $to->lastName);
        $this->assertEqualsWithDelta($this->fromPerson->age, $to->age, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function fromObject(): void
    {
        $mapper = self::$mapper->getMapper(IMapperToPerson::class);
        $to = $mapper->fromObject($this->personObject);

        $this->assertNotNull($to);
        $this->assertEquals($this->personObject->firstName, $to->firstName);
        $this->assertEquals($this->personObject->lastName, $to->lastName);
        $this->assertEquals($this->personObject->age, $to->age);
    }
}
