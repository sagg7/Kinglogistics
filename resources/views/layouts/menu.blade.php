<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500"/>
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
                <a href="#"><i class="fas fa-user"></i><span class="menu-title" data-i18n="User">Staff</span></a>
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
                <a href="#"><i class="fas fa-shipping-fast"></i><span class="menu-title" data-i18n="User">Shippers</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/shipper/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/shipper/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    <li>
                        <a href="/shipper/invoice">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Invoices</span>
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
                    <li>
                        <a href="/charge/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Charges</span>
                        </a>
                    </li>
                    <li>
                        <a href="/bonus/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Bonuses</span>
                        </a>
                    </li>
                    <li>
                        <a href="/loan/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Loans</span>
                        </a>
                    </li>
                    <li>
                        <a href="/charge/diesel">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Diesel</span>
                        </a>
                    </li>
                    <li>
                        <a href="/carrier/payment">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Payments</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="/driver/index"><i class="fas fa-id-card"></i><span class="menu-item" data-i18n="List">Drivers</span></a>
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
            <!--<li class="nav-item has-sub">
                <a href="#"><i class="fas fa-list-ol"></i><span class="menu-title" data-i18n="User">Trailer Types</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/trailerType/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/trailerType/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>-->
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-atlas"></i><span class="menu-title" data-i18n="User">Zones</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/zone/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/zone/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-folder-open"></i><span class="menu-title" data-i18n="User">Paperwork</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/paperwork/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/paperwork/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--<li class="nav-item has-sub">
                <a href="#"><i class="fas fa-map"></i><span class="menu-title" data-i18n="User">Trips</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/trip/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/trip/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>-->
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-truck-loading"></i><span class="menu-title" data-i18n="User">Loads</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/load/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/load/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    <li>
                        <a href="/trip/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Jobs</span>
                        </a>
                    </li>
                    <li>
                        <a href="/rate/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Rates</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-hand-holding-usd"></i><span class="menu-title" data-i18n="User">Job Opportunities</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/jobOpportunity/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/jobOpportunity/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-dollar-sign"></i><span class="menu-title" data-i18n="User">Expenses</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/expense/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/expense/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
            @if(auth()->user()->hasRole(['admin', 'operations', 'dispatch']))
                <li class="nav-item">
                    <a href="/tracking">
                        <i class="fas fa-map-marker-alt"></i><span class="menu-item" data-i18n="View">Tracking</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/chat"><i class="fas fa-comments"></i><span class="menu-title" data-i18n="User">Chat</span></a>
                </li>
            @endif
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
            <!--<li class="nav-item has-sub">
                <a href="#"><i class="fas fa-list-ol"></i><span class="menu-title" data-i18n="User">Incident Types</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/incidentType/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/incidentType/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>-->
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-envelope-open-text"></i><span class="menu-title" data-i18n="User">Messages</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/safetyMessage/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    <li>
                        <a href="/safetyMessage/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
