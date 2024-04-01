<!--**********************************
            Sidebar start
        ***********************************-->
<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">
            <li>
                <a href="{{ route('dashboard') }}" aria-expanded="false">
                    <i class="icon-home menu-icon"></i><span class="nav-text">Dashboard</span>
                </a>
            </li>
            @php
                $data = App\Models\RoleHasMenu::where('role_id', Auth::user()->role_id)->pluck('submenu_id');
                $submenus = App\Models\Submenu::whereIn('id', $data)->pluck('menu_id');
                $menus = App\Models\Menu::with([
                        'submenus' => function ($q) use ($data) {
                            $q->whereIn('id', $data);
                        },
                    ])
                    ->whereIn('id', $submenus)
                    ->get();
            @endphp
            @foreach ($menus as $item)
                <li>
                    <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                        <i class="{{ $item->icon }}"></i> <span class="nav-text">{{ $item->title }}</span>
                    </a>
                    <ul aria-expanded="false">
                        @foreach ($item->submenus as $row)
                            <li><a href="{{ route($row->submenuRoute) }}"><i class="{{ $row->submenuIcon }}"></i>
                                    {{ $row->submenuTitle }}</a></li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
            <li>
                <a href="{{ route('auth.logout') }}" aria-expanded="false">
                    <i class="icon-logout menu-icon"></i><span class="nav-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!--**********************************
            Sidebar end
        ***********************************-->
