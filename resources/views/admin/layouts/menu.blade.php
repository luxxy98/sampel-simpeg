<div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper"
     data-kt-scroll="true"
     data-kt-scroll-activate="{default: false, lg: true}"
     data-kt-scroll-height="auto"
     data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer"
     data-kt-scroll-wrappers="#kt_aside_menu"
     data-kt-scroll-offset="0">
    <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
         id="#kt_aside_menu"
         data-kt-menu="true">
        <div class="menu-item">
            <!-- Data Utama -->
            <a class="menu-link" href="{{ route('index') }}">
                <span class="menu-title">Dashboard</span>
            </a>
            <a class="menu-link {{ request()->routeIs('admin.person.index') ? 'active' : '' }}"
               href="{{ route('admin.person.index') }}">
                <span class="menu-title">Person</span>
            </a>
        </div>
    </div>
</div>
