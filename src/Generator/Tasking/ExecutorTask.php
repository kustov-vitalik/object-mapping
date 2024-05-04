<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Tasking;

final class ExecutorTask implements ITask
{
    /**
     * @var ITask[] 
     */
    private readonly array $tasks;

    public function __construct(ITask...$task)
    {
        $this->tasks = $task;
    }

    public function execute(): void
    {
        foreach ($this->tasks as $task) {
            if (($task instanceof IOptionalTask) && !$task->supports()) {
                continue;
            }

            $task->execute();

            if (($task instanceof IStoppableTask) && $task->isPropagationStopped()) {
                break;
            }
        }
    }
}
