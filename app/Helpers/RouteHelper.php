<?php

use Illuminate\Support\Str;

if (!function_exists('isMenuActive')) {
    function isMenuActive($item, $currentUrl)
    {
        $activeList = collect($item['children'] ?? [])->pluck('url')->toArray();
        $activeList = array_merge($activeList, $item['active'] ?? []);

        // Nếu currentUrl khớp chính xác hoặc bắt đầu bằng các URL trong danh sách
        foreach ($activeList as $active) {
            if (Str::startsWith("/$currentUrl", $active)) {
                return true;
            }
        }

        // Nếu url chính không phải javascript và trùng current url
        return ($item['url'] !== 'javascript:void(0)' && $currentUrl === ltrim($item['url'], '/'));
    }
}


if (!function_exists('isChildActive')) {

    function isChildActive($child, $currentUrl)
    {
        return $currentUrl === ltrim($child['url'], '/');
    }
}
