<div class="sidebar-header">
    <img src="{{ showImage($setting->logo) }}" class="logo-icon" alt="logo icon" />
</div>
<!--navigation-->
@php
    $menu = json_decode(file_get_contents(resource_path('views/backend/data/menu.json')), true);
    $currentUrl = request()->path(); // VD: 'employees' (không có /)
@endphp

<ul class="metismenu" id="menu">
    @foreach ($menu as $item)
        @php
            // Kiểm tra nếu là cha có children, và có item con trùng url hiện tại => mở ra
            $hasActiveChild = false;
            if (isset($item['children'])) {
                foreach ($item['children'] as &$child) {
                    $childUrl = ltrim($child['url'], '/');
                    if ($currentUrl === $childUrl) {
                        $child['active'] = true;
                        $hasActiveChild = true;
                    } else {
                        $child['active'] = false;
                    }
                }

                unset($child);
            }
            $isActive = $hasActiveChild || ltrim($item['url'], '/') === $currentUrl;
        @endphp

        <li class="{{ $isActive ? 'mm-active' : '' }}">
            <a href="{{ $item['url'] }}" class="{{ isset($item['children']) ? 'has-arrow' : '' }}">
                <div class="parent-icon">
                    <i class="{{ $item['icon'] }}"></i>
                </div>
                <div class="menu-title">{{ $item['title'] }}</div>
            </a>

            @if (isset($item['children']))
                <ul>
                    @foreach ($item['children'] as $child)
                        <li class="{{ $child['active'] ? 'active' : '' }}">
                            <a href="{{ $child['url'] }}">
                                {{ $child['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
<!--end navigation-->
