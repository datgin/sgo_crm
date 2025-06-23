<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('generateUniqueCode')) {
    function generateUniqueCode(string $table, string $column = 'code', int $length = 12): string
    {
        do {
            $code = generateCode($length);
        } while (DB::table($table)->where($column, $code)->exists());

        return $code;
    }
}

if (!function_exists('generateCode')) {

    function generateCode(int $length = 12): string
    {
        $base = microtime(true) . bin2hex(random_bytes(5));
        $hash = strtoupper(substr(hash('sha256', $base), 0, $length));
        return $hash;
    }
}

if (!function_exists('formatPrice')) {

    function formatPrice($amount, bool $showCurrency = false): string
    {
        if (empty($amount)) return $showCurrency ? "0 ₫" : 0;

        $formatted = number_format($amount, 0, ',', '.');
        return $showCurrency ? "$formatted ₫" : $formatted;
    }
}
