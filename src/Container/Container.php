<?php

namespace App\Container;

class Container
{
    private array $services = [];

    public function set(string $id, callable $factory): void
    {
        $this->services[$id] = $factory;
    }

    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new \RuntimeException("Service '$id' not found.");
        }

        return $this->services[$id]($this);
    }
}