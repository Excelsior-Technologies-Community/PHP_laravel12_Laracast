<?php

namespace App\Services;


class TypeSafeService
{
    public function formatNumber(int $number): string
    {
        return "Number is: " . $number;
    }

    public function multiply(int $a, int $b): int
    {
        return $a * $b;
    }
}