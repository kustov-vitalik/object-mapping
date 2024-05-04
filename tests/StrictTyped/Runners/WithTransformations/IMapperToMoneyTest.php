<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Runners\WithTransformations;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VKPHPUtils\Mapping\Mappers;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\WithTransformations\IMapperToMoney;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromMoney;

class IMapperToMoneyTest extends TestCase
{
    #[Test]
    public function fromMoney(): void
    {
        $mapper = Mappers::strictTyped()->getMapper(IMapperToMoney::class);

        $toMoney = $mapper->fromMoney($fromMoney = new FromMoney());

        $this->assertNotNull($toMoney);
        $this->assertSame($fromMoney->amount, $toMoney->amount);
        $this->assertSame($fromMoney->currency, $toMoney->currency->value);
    }
}
