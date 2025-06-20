<?php

if (!function_exists('isMenuActive')) {
    function isMenuActive($item, $currentUrl)
    {
        // Ghép children + active nếu có
        $activeList = collect($item['children'] ?? [])->pluck('url')->toArray();
        $activeList = array_merge($activeList, $item['active'] ?? []);

        // Nếu URL hiện tại khớp url chính hoặc url con
        return in_array("/$currentUrl", $activeList) || ($item['url'] !== 'javascript:void(0)' && $currentUrl === ltrim($item['url'], '/'));
    }
}


if (!function_exists('isChildActive')) {

    function isChildActive($child, $currentUrl)
    {
        return $currentUrl === ltrim($child['url'], '/');
    }
}
