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
            @if(auth()->user()->can(['create-rental', 'read-rental']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-file-alt"></i><span class="menu-title" data-i18n="User">Rentals</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-rental']))
                    <li>
                        <a href="/rental/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-rental']))
                    <li>
                        <a href="/rental/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['create-staff', 'read-staff', 'read-dispatch-schedule']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-user"></i><span class="menu-title" data-i18n="User">Staff</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-staff']))
                    <li>
                        <a href="/user/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-staff']))
                    <li>
                        <a href="/user/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-dispatch-schedule']))
                    <li>
                        <a href="/user/dispatchSchedule">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Dispatch Schedule</span>
                        </a>
                    </li>
                    @endif
                    {{-- @if(auth()->user()->hasRole('spotter')||auth()->user()->hasRole('admin')) --}}
                    <li>
                        <a href="/user/spotterCheckInOut">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Check In/out</span>
                        </a>
                    </li>
                    {{-- @endif --}}
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['create-customer', 'read-customer', 'read-invoice']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-shipping-fast"></i><span class="menu-title" data-i18n="User">Customers</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-customer']))
                    <li>
                        <a href="/shipper/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-customer']))
                    <li>
                        <a href="/shipper/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-invoice']))
                    <li>
                        <a href="/shipper/invoice">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Invoices</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['create-carrier', 'read-carrier']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-dolly-flatbed"></i><span class="menu-title" data-i18n="User">Carriers</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-carrier']))
                        <li>
                            <a href="/carrier/create">
                                <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->can(['read-carrier']))
                        <li>
                            <a href="/carrier/index">
                                <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->can(['read-statement']))
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
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['create-driver', 'read-driver']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-id-card"></i><span class="menu-title" data-i18n="User">Drivers</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-driver']))
                    <li>
                        <a href="/driver/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-driver']))
                    <li>
                        <a href="/driver/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['create-trailer', 'read-trailer']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-trailer"></i><span class="menu-title" data-i18n="User">Trailers</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-trailer']))
                    <li>
                        <a href="/trailer/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-trailer']))
                    <li>
                        <a href="/trailer/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['create-truck', 'read-truck']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-truck-moving"></i><span class="menu-title" data-i18n="User">Trucks</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-truck']))
                    <li>
                        <a href="/truck/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-truck']))
                    <li>
                        <a href="/truck/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
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
            @if(auth()->user()->can(['create-zone', 'read-zone']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-atlas"></i><span class="menu-title" data-i18n="User">Zones</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-zone']))
                    <li>
                        <a href="/zone/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-zone']))
                    <li>
                        <a href="/zone/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['create-paperwork', 'read-paperwork']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-folder-open"></i><span class="menu-title" data-i18n="User">Paperwork</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-paperwork']))
                    <li>
                        <a href="/paperwork/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-paperwork']))
                    <li>
                        <a href="/paperwork/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
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
            @if(auth()->user()->can(['create-load', 'read-load', 'create-load-dispatch', 'read-load-dispatch', 'read-job', 'read-rate']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-truck-loading"></i><span class="menu-title" data-i18n="User">Loads</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-load']))
                    <li>
                        <a href="/load/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-load']))
                    <li>
                        <a href="/load/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-load-dispatch']))
                    <li>
                        <a href="/load/indexDispatch">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View loads Dispatch</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-job']))
                    <li class="has-sub">
                        <a href="#">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Jobs</span>
                        </a>
                        <ul class="menu-content">
                            <li class="pl-1">
                                <a href="/trip/index">
                                    <i class="fas fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                                </a>
                            </li>
                            <li class="pl-1">
                                <a href="/trip/origin/index">
                                    <i class="fas fa-circle"></i><span class="menu-item" data-i18n="View">Origins</span>
                                </a>
                            </li>
                            <li class="pl-1">
                                <a href="/trip/destination/index">
                                    <i class="fas fa-circle"></i><span class="menu-item" data-i18n="View">Destinations</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-rate']))
                    <li>
                        <a href="/rate/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Rates</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-clipboard-list"></i><span class="menu-title">Load Board</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/load/road/index">
                            <i class="far fa-circle"></i><span class="menu-item">View</span>
                        </a>
                    </li>
                    <li>
                        <a href="/load/road/dispatch/index">
                            <i class="far fa-circle"></i><span class="menu-item">View Dispatch</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['create-job-opportunity', 'read-job-opportunity']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-hand-holding-usd"></i><span class="menu-title" data-i18n="User">Job Opportunities</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-job-opportunity']))
                    <li>
                        <a href="/jobOpportunity/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-job-opportunity']))
                    <li>
                        <a href="/jobOpportunity/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['read-expense', 'read-income']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-dollar-sign"></i><span class="menu-title" data-i18n="User">Accounting</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['read-expense']))
                    <li>
                        <a href="/expense/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Expenses</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-income']))
                    <li>
                        <a href="/income/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">Income</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['read-tracking', 'read-tracking-history']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-map-marker-alt"></i><span class="menu-item" data-i18n="View">Tracking</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['read-tracking']))
                    <li>
                        <a href="/tracking">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Real time</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-tracking-history']))
                    <li>
                        <a href="/tracking/history">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">History</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->can(['read-chat']))
                <li class="nav-item">
                    <a href="/chat"><i class="fas fa-comments"></i><span class="menu-title" data-i18n="User">Chat</span></a>
                </li>
            @endif
            @if(auth()->user()->can(['read-report-daily-loads']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-chart-bar"></i><span class="menu-title" data-i18n="User">Reports</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/report/dailyLoads">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Daily Loads</span>
                        </a>
                    </li>
                    <li>
                        <a href="/report/activeTime">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Active Time</span>
                        </a>
                    </li>
                    <li>
                        <a href="/report/profitAndLoss">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Profit and Loss</span>
                        </a>
                    </li>
                    <li>
                        <a href="/report/customerLoads">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Customer Loads</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            <li class="navigation-header">
                <span>Safety</span>
            </li>
            @if(auth()->user()->can(['create-incident', 'read-incident']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-exclamation-circle"></i><span class="menu-title" data-i18n="User">Incident</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-incident']))
                    <li>
                        <a href="/incident/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-incident']))
                    <li>
                        <a href="/incident/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
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
            @if(auth()->user()->can(['create-safety-messages', 'read-safety-messages']))
            <li class="nav-item has-sub">
                <a href="#"><i class="fas fa-envelope-open-text"></i><span class="menu-title" data-i18n="User">Messages</span></a>
                <ul class="menu-content">
                    @if(auth()->user()->can(['create-safety-messages']))
                    <li>
                        <a href="/safetyMessage/create">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="List">Create</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(['read-safety-messages']))
                    <li>
                        <a href="/safetyMessage/index">
                            <i class="far fa-circle"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
