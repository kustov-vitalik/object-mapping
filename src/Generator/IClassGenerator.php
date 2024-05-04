<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator;

use PhpParser\Node;

interface IClassGenerator
{
    public function generateMapperClass(): Node;
}
