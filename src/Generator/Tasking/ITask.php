<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Tasking;

interface ITask
{
    public function execute(): void;
}
