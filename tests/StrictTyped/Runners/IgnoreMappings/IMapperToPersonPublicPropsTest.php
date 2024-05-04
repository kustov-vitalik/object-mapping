<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\IgnoreMappings;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\IgnoreMapping\IMapperToPersonPublicProps;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromSimplePerson;

class IMapperToPersonPublicPropsTest extends TestCase
{
    #[Test]
    public function fromSimplePersonThenFillBirthDateThenFillFirstName(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperToPersonPublicProps::class);
        $fromSimplePerson = new FromSimplePerson();
        $to = $mapper->fromSimplePerson($fromSimplePerson);

        $this->assertNotNull($to);
        $this->assertSame($fromSimplePerson->firstName, $to->firstName);
        $this->assertSame($fromSimplePerson->lastName, $to->lastName);

        $this->assertFalse(isset($to->birth));

        $mapper->fillBirthDate($to, $dateTimeImmutable = new DateTimeImmutable());
        $this->assertNotNull($to->birth);
        $this->assertEquals($dateTimeImmutable, $to->birth);

        $mapper->fillFirstName($firstName = 'John', $to);
        $this->assertSame($firstName, $to->firstName);
        $this->assertSame($fromSimplePerson->lastName, $to->lastName);
        $this->assertEquals($dateTimeImmutable, $to->birth);
    }
}
