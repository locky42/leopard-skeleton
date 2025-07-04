<?php

namespace App\Core;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $instances = [];
    private array $definitions = [];

    public function set(string $id, callable $definition): void
    {
        $this->definitions[$id] = $definition;
    }

    public function get(string $id): object
    {
        if (!isset($this->instances[$id])) {
            if (isset($this->definitions[$id])) {
                $this->instances[$id] = ($this->definitions[$id])($this);
            } else {
                if (!class_exists($id)) {
                    throw new \RuntimeException("Class $id does not exist.");
                }
                $this->instances[$id] = new $id();
            }
        }
        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]) || class_exists($id);
    }
}
