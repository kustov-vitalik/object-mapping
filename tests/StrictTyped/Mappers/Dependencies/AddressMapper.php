<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Dependencies;

use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromAddress;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToAddress;

class AddressMapper
{
    public function mapAddress(FromAddress $fromAddress): ToAddress
    {
        $toAddress = new ToAddress();
        $toAddress->city = $fromAddress->city;
        $toAddress->country = $fromAddress->country;
        return $toAddress;
    }
}
