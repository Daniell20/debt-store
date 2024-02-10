<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="#" class="text-nowrap logo-img">
                <img src="{{ asset('images/logos/debstorelogo.png') }}" width="100%" alt="" />
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('users.dashboard') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('users.shop') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-building-store"></i>
                        </span>
                        <span class="hide-menu">Shop</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('report.index') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-message-report"></i>
                        </span>
                        <span class="hide-menu">Report</span>
                    </a>
                </li>
                {{-- <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Account</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('users.details') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-list-details"></i>
                        </span>
                        <span class="hide-menu">Details</span>
                    </a>
                </li> --}}
            </ul>
        </nav>
    </div>
</aside>
