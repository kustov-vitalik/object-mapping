<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\DS;

use SplStack;
use SplQueue;
class DirectedGraph
{
    /**
     * @var list<mixed> 
     */
    private array $data = [];

    private array $adjacencyLists = [[]];

    private array $vertices = [];

    private int $index = 0;

    private readonly SplStack $stack;

    private array $numbers = [];

    private array $lowLinks = [];

    private array $stronglyConnectedComponents = [];

    private int $size = 0;

    public function __construct()
    {
        $this->stack = new SplStack();
    }

    public function addDirectedEdge(mixed $from, mixed $to): void
    {
        $indexFrom = array_search($from, $this->data, false);
        if ($indexFrom === false) {
            $indexFrom = count($this->data);
            $this->data[] = $from;
        }

        $indexTo = array_search($to, $this->data, false);
        if ($indexTo === false) {
            $indexTo = count($this->data);
            $this->data[] = $to;
        }

        $this->adjacencyLists[$indexFrom][] = $indexTo;

        $this->size = max($indexFrom, $indexTo) + 1;
    }

    /**
     * Get the path from one vertex (edge) to another.
     *
     * @param  mixed $from The starting vertex (edge).
     * @param  mixed $to   The ending vertex (edge).
     * @return array<int, mixed> The path from $from to $to. If the path doesn't exist, return an empty array.
     */
    public function getPath(mixed $from, mixed $to): array
    {
        $indexFrom = array_search($from, $this->data, true);
        $indexTo = array_search($to, $this->data, true);

        if ($indexFrom === false || $indexTo === false) {
            return [];
        }

        $visited = array_fill(0, $this->size, false);
        $queue = new SplQueue();
        $queue->enqueue([$indexFrom]);

        while (!$queue->isEmpty()) {
            $path = $queue->dequeue();
            $lastVertex = end($path);

            if ($lastVertex === $indexTo) {
                // We found the path from $from to $to.
                return array_map(fn(int $idx): mixed => $this->data[$idx], $path);
            }

            foreach ($this->adjacencyLists[$lastVertex] as $adjacent) {
                if (!$visited[$adjacent]) {
                    $visited[$adjacent] = true;
                    $newPath = $path;
                    $newPath[] = $adjacent;
                    $queue->enqueue($newPath);
                }
            }
        }

        // No path from $from to $to was found.
        return [];
    }

    /**
     * @return array<int, mixed>
     */
    public function getStronglyConnectedComponents(): array
    {
        $this->adjust();

        foreach ($this->vertices as $vertex) {
            if (!isset($this->numbers[$vertex])) {
                $this->stronglyConnect($vertex);
            }
        }

        return array_map(
            fn(array $ids): array => array_map(fn(int $idx): mixed => $this->data[$idx], $ids),
            $this->stronglyConnectedComponents
        );
    }

    private function adjust(): void
    {
        for ($i = 0; $i < $this->size; ++$i) {
            $this->vertices[] = $i;
            if (!array_key_exists($i, $this->adjacencyLists)) {
                $this->adjacencyLists[$i] = [];
            }
        }
    }

    private function stronglyConnect(int $vertex, array &$visited = []): void
    {
        $this->numbers[$vertex] = $this->index;
        $this->lowLinks[$vertex] = $this->index;
        ++$this->index;
        $this->stack->push($vertex);
        $visited[$vertex] = true;

        foreach ($this->adjacencyLists[$vertex] as $adjacent) {
            if (!isset($this->numbers[$adjacent])) {
                $this->stronglyConnect($adjacent, $visited);
                $this->lowLinks[$vertex] = min($this->lowLinks[$vertex], $this->lowLinks[$adjacent]);
            } elseif (isset($visited[$adjacent])) {
                $this->lowLinks[$vertex] = min($this->lowLinks[$vertex], $this->numbers[$adjacent]);
            }
        }

        if ($this->lowLinks[$vertex] === $this->numbers[$vertex]) {
            $stronglyConnectedComponent = [];
            do {
                $top = $this->stack->pop();
                unset($visited[$top]);
                $stronglyConnectedComponent[] = $top;
            } while ($top !== $vertex);

            $this->stronglyConnectedComponents[] = $stronglyConnectedComponent;
        }
    }

    public function isCyclic(): bool
    {
        $this->adjust();
        $visited = array_fill(0, $this->size, false);
        $recStack = array_fill(0, $this->size, false);

        foreach ($this->vertices as $vertex) {
            if ($visited[$vertex]) {
                continue;
            }

            if (!$this->isCyclicHelper($vertex, $visited, $recStack)) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function isCyclicHelper(int $vertex, array &$visited, array &$recStack): bool
    {
        $visited[$vertex] = true;
        $recStack[$vertex] = true;

        foreach ($this->adjacencyLists[$vertex] as $adjacent) {
            if (!$visited[$adjacent]) {
                if ($this->isCyclicHelper($adjacent, $visited, $recStack)) {
                    return true;
                }
            } elseif ($recStack[$adjacent]) {
                return true;
            }
        }

        $recStack[$vertex] = false;
        return false;
    }
}
