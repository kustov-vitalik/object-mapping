<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\StrictTyped\Objects\To;

use DateTimeImmutable;

class ToPersonPublicProps
{
    public string $firstName;

    public string $lastName;

    public DateTimeImmutable $birth;
}
