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
                    <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary"
                       data-ticon="icon-disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item">
                <a href="/">
                    <i class="fa fa-home"></i>
                    <span class="menu-title" data-i18n="Dashboard">Inicio</span>
                </a>
            </li>
            <li class="nav-item has-sub">
                <a href="#"><i class="fa fa-user"></i><span class="menu-title" data-i18n="User">User</span></a>
                <ul class="menu-content">
                    <li>
                        <a href="/user">
                            <i class="fa fa-circle-o"></i><span class="menu-item" data-i18n="List">List</span>
                        </a>
                    </li>
                    <li>
                        <a href="/user/create">
                            <i class="fa fa-circle-o"></i><span class="menu-item" data-i18n="View">View</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
