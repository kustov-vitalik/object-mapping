<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped;

use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\IMapperToPersonPublicProps;

class SetupTest extends TestCase
{
    public function testSetup(): void
    {
        $mapper = Mappers::strictTyped();

        $simpleMapper = $mapper->getMapper(IMapperToPersonPublicProps::class);

        $this->assertNotNull($simpleMapper);
    }
}
