<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator;

use VKPHPUtils\Mapping\Exception\InvalidConfigException;

interface IValidator
{
    /**
     * @throws InvalidConfigException
     */
    public function validate(): void;
}
