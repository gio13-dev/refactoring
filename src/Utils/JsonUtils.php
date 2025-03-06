<?php

namespace App\Utils;

use RuntimeException;

class JsonUtils
{
    public static function decode(string $json): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('JSON decode error: ' . json_last_error_msg());
        }

        return $data;
    }
}