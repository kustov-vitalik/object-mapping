<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To;

class ToPersonWithAddress
{
    private ToAddress $address;

    private string $firstName;

    public function getAddress(): ToAddress
    {
        return $this->address;
    }

    public function setAddress(ToAddress $toAddress): void
    {
        $this->address = $toAddress;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }
}
