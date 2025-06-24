<?php

namespace App\Core;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $instances = [];

    public function get(string $id): object
    {
        if (!isset($this->instances[$id])) {
            $this->instances[$id] = new $id();
        }
        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return class_exists($id);
    }
}
