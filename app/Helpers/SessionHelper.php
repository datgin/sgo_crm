<?php

if (!function_exists('sessionFlash')) {
    function sessionFlash(string $key, string $value)
    {
        session()->flash($key, $value);
    }
}
