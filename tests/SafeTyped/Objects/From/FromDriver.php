<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Tests\SafeTyped\Objects\From;

readonly class FromDriver
{
    public function __construct(
        public FromFullName $fullName = new FromFullName('Will Smith'),
        private FromPerson $father = new FromPerson('John', 'Smith', 65),
    ) {
    }

    /**
     * @return FromPerson
     */
    public function getFather(): FromPerson
    {
        return $this->father;
    }
}
