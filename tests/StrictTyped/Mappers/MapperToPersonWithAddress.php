<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Mappers;

use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Attributes\Mapping;
use VKPHPUtils\Mapping\Attributes\Qualifier;
use VKPHPUtils\Mapping\Tests\StrictTyped\Mappers\Dependencies\AddressMapper;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromAddress;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\From\FromPersonWithAddress;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToAddress;
use VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To\ToPersonWithAddress;

#[Mapper]
abstract class MapperToPersonWithAddress
{
    public function __construct(private readonly AddressMapper $addressMapper)
    {
    }

    #[Mapping(target: 'address', qualifier: new Qualifier('mapAddress'))]
    abstract public function map(FromPersonWithAddress $person): ToPersonWithAddress;

    public function mapAddress(FromAddress $fromAddress): ToAddress
    {
        return $this->addressMapper->mapAddress($fromAddress);
    }
}
