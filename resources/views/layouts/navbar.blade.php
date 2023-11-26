<!--  Header Start -->
<?php
    $user = Auth::user();
    if ($user->is_admin) {
        $user_name = "Master Admin";
        $route = "#";
    } else if ($user->is_customer) {
        $customer = \App\Customer::where("user_id", Auth::user()->id)->select("name")->first();
        $user_name = $customer->name;
        $route = url("customer/users-profile");
    } else {
        $merchant = \App\Merchant::where("user_id", Auth::user()->id)->select("name")->first();
        $user_name = $merchant->name;
        $route = url("merchant/users-profile");
    }
?>

<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse"
                    href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
        </ul>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset($user->profile_picture) }}" alt="" width="35" height="35" class="rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                        aria-labelledby="drop2">
                        <div class="message-body">
                            <a href="{{ $route }}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">Hello {{ mb_convert_case($user_name, MB_CASE_TITLE, "UTF-8") }}</p>
                            </a>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>