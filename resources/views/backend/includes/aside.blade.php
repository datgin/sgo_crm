<div class="sidebar-header">
    <img src="{{ fileExists($setting->logo) }}" class="logo-icon" alt="logo icon" />
</div>
<!--navigation-->
@php
    $menus = json_decode(file_get_contents(resource_path('views/backend/data/menu.json')), true);
    $currentUrl = request()->path(); // VD: 'employees' (không có /)
@endphp

<ul class="metismenu" id="menu">
    @foreach ($menus as $item)
        @php
            $open = isMenuActive($item, $currentUrl) ? 'mm-active' : '';
            logger($open);
        @endphp
        <li class="{{ $open }}">
            <a href="{{ $item['url'] }}" class="{{ isset($item['children']) ? 'has-arrow' : '' }}">
                <div class="parent-icon">
                    <i class="{{ $item['icon'] }}"></i>
                </div>
                <div class="menu-title">{{ $item['title'] }}</div>
            </a>

            @if (isset($item['children']))
                <ul>
                    @foreach ($item['children'] as $child)
                        @php
                            $isAdmin = auth('admin')->user()->isAdmin();
                            $childIsAdmin = $child['is_admin'] ?? null;
                        @endphp

                        @if ($childIsAdmin === null || $childIsAdmin === $isAdmin)
                            <li class="{{ isChildActive($child, $currentUrl) ? 'mm-active' : '' }}">
                                <a href="{{ $child['url'] }}">
                                    <i class="far fa-dot-circle me-2"></i> {{ $child['title'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif

        </li>
    @endforeach
</ul>

<!--end navigation-->
