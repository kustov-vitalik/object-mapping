<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Tasking;

interface IOptionalTask extends ITask
{
    public function supports(): bool;
}
