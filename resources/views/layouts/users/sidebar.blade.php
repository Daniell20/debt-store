<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            {{-- <a href="./index.html" class="text-nowrap logo-img">
                <img src="{{ asset('images/logos/dark-logo.svg') }}" width="180" alt="" />
            </a> --}}
            <a href="">Imong logo diri</a>
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
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">DEBT STORE USER COMPONENTS</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('users.store') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-building-store"></i>
                        </span>
                        <span class="hide-menu">My Store</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>