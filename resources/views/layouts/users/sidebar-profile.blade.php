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
					<?php 
						if (Auth::user()->is_merchant) {
							$route = route("merchant.dashboard");
						} else if (Auth::user()->is_customer) {
							$route = route("users.dashboard");
						}
					?>
                    <a href="{{ $route }}" class="hide-menu" style="cursor: pointer;">Home</a>
                </li>
			</ul>
		</nav>
    </div>
</aside>