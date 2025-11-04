<div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
    data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
    data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
    data-kt-scroll-offset="0">
    <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
        id="#kt_aside_menu" data-kt-menu="true">
        <div class="menu-item">
            <!-- Data Utama -->
            <a class="menu-link" href="{{ route('index') }}">
                <span class="menu-title">Dashboard</span>
            </a>
            <a class="menu-link {{ request()->routeIs('admin.person.index') ? 'active' : '' }}"
                href="{{ route('admin.person.index') }}">
                <span class="menu-title">Person</span>
            </a>
            <a class="menu-link {{ request()->routeIs('admin.sdm.sdm.index') ? 'active' : '' }}"
                href="{{ route('admin.sdm.sdm.index') }}">
                <span class="menu-title">SDM</span>
            </a>
            @php
                $referensiActive = request()->routeIs('admin.ref.jenjang-pendidikan.*') ||
                                   request()->routeIs('admin.ref.hubungan-keluarga.*');
            @endphp
            <div data-kt-menu-trigger="click"
                 class="menu-item menu-accordion {{ $referensiActive ? 'here show' : '' }}">
                <span class="menu-link">
                    <span class="menu-title">Referensi</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion menu-active-bg">
                    <a class="menu-link {{ request()->routeIs('admin.ref.jenjang-pendidikan.*') ? 'active' : '' }}"
                       href="{{ route('admin.ref.jenjang-pendidikan.index') }}">
                        <span class="menu-title px-4">Jenjang Pendidikan</span>
                    </a>
                    <a class="menu-link {{ request()->routeIs('admin.ref.hubungan-keluarga.*') ? 'active' : '' }}"
                       href="{{ route('admin.ref.hubungan-keluarga.index') }}">
                        <span class="menu-title px-4">Hubungan Keluarga</span>
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</div>
