<?php

namespace App\Config;

class Config
{
    private array $config;

    public function __construct(string $configFile)
    {
        if (!file_exists($configFile)) {
            throw new \RuntimeException("Configuration file not found: $configFile");
        }

        $this->config = require $configFile;
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);

        $value = $this->config;

        foreach ($keys as $nestedKey) {
            if (!isset($value[$nestedKey])) {
                return $default;
            }
            $value = $value[$nestedKey];
        }

        return $value;
    }
}
