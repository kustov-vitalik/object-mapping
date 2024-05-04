<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\MapMapping;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\MapMapping\IMapperMapToSuperSimplePerson;

class IMapperMapToSuperSimplePersonTest extends TestCase
{
    #[Test]
    public function fromArray(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperMapToSuperSimplePerson::class);

        $person = $mapper->fromArray(
            $from = [
                'firstName' => 'fName',
            ]
        );

        $this->assertNotNull($person);
        $this->assertSame($from['firstName'], $person->firstName);
    }
}
