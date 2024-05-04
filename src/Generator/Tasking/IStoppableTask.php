<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Tasking;

interface IStoppableTask extends ITask
{
    public function isPropagationStopped(): bool;
}
