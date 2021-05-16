<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="/">
                    <!--<x-application-logo class="w-20 h-20 fill-current text-gray-500"/>-->
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                    <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary" data-ticon="icon-disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item">
                <a href="/">
                    <i class="fas fa-home"></i>
                    <span class="menu-title" data-i18n="Dashboard">Home</span>
                </a>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-file-alt"></i><span class="menu-title" data-i18n="User">Rentals</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/rental/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/rental/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-user"></i><span class="menu-title" data-i18n="User">Users</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/user/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/user/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-dolly-flatbed"></i><span class="menu-title" data-i18n="User">Carriers</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/carrier/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/carrier/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-id-card"></i><span class="menu-title" data-i18n="User">Drivers</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/driver/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/driver/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-trailer"></i><span class="menu-title" data-i18n="User">Trailers</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/trailer/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/trailer/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-truck"></i><span class="menu-title" data-i18n="User">Trucks</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/truck/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/truck/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="navigation-header">
                <span>Safety</span>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-exclamation-circle"></i><span class="menu-title" data-i18n="User">Incident</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/incident/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/incident/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-envelope-open-text"></i><span class="menu-title" data-i18n="User">Messages</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/notification/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/notification/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
