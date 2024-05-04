<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator;

use PhpParser\Node;

interface IInitiationCodeGenerator
{

    /**
     * @return Node[]
     */
    public function generateInitiationCode(string $instanceClassName): array;

}
