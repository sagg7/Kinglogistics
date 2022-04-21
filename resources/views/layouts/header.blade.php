<!-- BEGIN: Header-->
<div class="content-overlay"></div>
<div class="header-navbar-shadow"></div>
<nav class="@if(session('fillDocumentation')){{ "header-navbar navbar-expand-lg navbar navbar-with-menu navbar-fixed navbar-shadow navbar-brand-center" }}@else{{ "header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow" }}@endif">
    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    @if(!session('fillDocumentation'))
                    <ul class="nav navbar-nav">
                        <li class="nav-item mobile-menu d-xl-none mr-auto">
                            <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#">
                                <i class="ficon fa fa-bars"></i>
                            </a>
                        </li>
                    </ul>
                    @endif
                    <ul class="nav navbar-nav">
                        <li class="nav-item d-none d-lg-block">
                            <img src="{{ asset("images/allstar/logo-blue.png") }}" alt="King Logistic Oil" style="max-height: 40px;">
                        </li>
                    </ul>
                </div>
                <ul class="nav navbar-nav float-right">
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a>
                    </li>
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <div class="user-nav d-sm-flex d-none">
                                <span class="user-name text-bold-600">{{-- auth()->user()->getFullName() --}}</span>
                                <!--<span class="user-status">Disponible</span>-->
                            </div>
                            <span class="user-pic">
                                @if(auth()->user()->profile_img ?? false)
                                    <img class="round" src="{{ auth()->user()->profile_img }}" alt="avatar" height="40"
                                         width="40">
                                @else
                                    <i class="fas fa-user-circle"></i>
                                @endif
                            </span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <div class="dropdown-menu dropdown-menu-right">
                            @if(auth()->guard('web')->check())
                                @if(auth()->user()->hasRole('admin'))
                                    <a class="dropdown-item" href="/company/profile"><i class="fas fa-user"></i>Company Profile</a>
                                @endif
                                <a class="dropdown-item" href="/user/profile"><i class="fas fa-user"></i>My Profile</a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-power-off"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- END: Header-->
