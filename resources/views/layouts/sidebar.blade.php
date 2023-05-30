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
                    <a class="sidebar-link" href="{{ route('merchant.dashboard') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">DEBT STORE COMPONENTS</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('merchant.store.index') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-building-store"></i>
                        </span>
                        <span class="hide-menu">Stores</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('merchant.product.index') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-shopping-cart"></i>
                        </span>
                        <span class="hide-menu">Products</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('merchant.customer.index') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="hide-menu">Customers</span>
                    </a>
                </li>
                
                {{-- <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('settings.index') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-adjustments-alt"></i>
                        </span>
                        <span class="hide-menu">Settings</span>
                    </a>
                </li> --}}
            </ul>
        </nav>
    </div>
</aside>