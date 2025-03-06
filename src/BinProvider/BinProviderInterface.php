<?php

namespace App\BinProvider;

interface BinProviderInterface
{
    public function getBinDetails(string $bin): array;
}
