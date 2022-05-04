<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="figure-1"></div>
    <div class="navbar-header">
        <div class="row align-items-center">
            <div class="col pr-0">
                <div class="nav-item mr-auto">
                    <a class="navbar-brand mt-1 mb-1" href="/">
                        <x-application-logo/>
                    </a>
                </div>
            </div>
            <div class="col-auto d-xl-none">
                <div class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                        <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                        <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary" data-ticon="icon-disc"></i>
                    </a>
                </div>
            </div>
        </div>
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
            <li class="nav-item">
                <a href="/profile"><i class="fas fa-user"></i><span class="menu-title">Profile</span></a>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-clipboard-list"></i><span class="menu-title">Load Board</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/load/road/index">
                            <i class="far fa-circle"></i><span class="menu-item">View</span>
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
                <a href="#"><i class="fas fa-dollar-sign"></i><span class="menu-title" data-i18n="User">Accounting</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/expense/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Expenses</span>
                        </a>
                    </li>
                    <li>
                        <a href="/payment/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Payments</span>
                        </a>
                    </li>
                    <li>
                        <a href="/dailyPay/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Daily Pay</span>
                        </a>
                    </li>
                    <li>
                        <a href="/loan/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Loans</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-map-marker-alt"></i><span class="menu-item" data-i18n="View">Tracking</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/tracking">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Real time</span>
                        </a>
                    </li>
                    <li>
                        <a href="/tracking/history">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">History</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-hand-holding-usd"></i><span class="menu-title" data-i18n="User">Job Opportunities</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/jobOpportunity/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-chart-bar"></i><span class="menu-title" data-i18n="User">Reports</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/report/historical">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Historical</span>
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
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
