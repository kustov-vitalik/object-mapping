<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\WithTransformations;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromMoney;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToMoney;

#[Mapper]
interface IMapperToMoney
{
    public function fromMoney(FromMoney $fromMoney): ToMoney;
}
