@include('partials.head')
@include('partials.preloader')
<!--**********************************
        Main wrapper start
    ***********************************-->
<div id="main-wrapper">
    <!--**********************************
                Nav header start
            ***********************************-->
    <div class="nav-header">
        <div class="brand-logo">
            <a href="{{ route('auth.index') }}">
                <b class="logo-abbr"><img src="{{ asset('dist/images/logo-csp-birumerah.gif') }}" width="55"
                        alt="Logo"> </b>
                <span class="logo-compact"><img src="{{ asset('dist/images/logo-csp-birumerah.gif') }}" width="55"
                        alt="Logo"></span>
                <span class="brand-title">
                    <img src="{{ asset('dist/images/logo-csp-birumerah.gif') }}" width="55" alt="Brand">
                </span>
            </a>
        </div>
    </div>
    <!--**********************************
                Nav header end
            ***********************************-->
    <!--**********************************
                Header start
            ***********************************-->
    <div class="header">
        <div class="header-content clearfix">
            <div class="nav-control">
                <div class="hamburger">
                    <span class="toggle-icon"><i class="icon-menu"></i></span>
                </div>
            </div>
            <div class="header-right">
                <ul class="clearfix">
                    <li class="icons dropdown">
                        <div class="user-img c-pointer position-relative" data-toggle="dropdown">
                            <button onclick="openLoginModal()" class="btn btn-primary">Sign in <i
                                    class="icon-login"></i></button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--**********************************
                Header end ti-comment-alt
            ***********************************-->
    <!--**********************************
            Sidebar start
        ***********************************-->
    <div class="nk-sidebar">
        <div class="nk-nav-scroll">
            <ul class="metismenu" id="menu">
                <li>
                    <a href="{{ route('auth.index') }}" aria-expanded="false">
                        <i class="icon-home menu-icon"></i><span class="nav-text">Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!--**********************************
            Sidebar end
        ***********************************-->
    @yield('content')
    @include('partials.footer')
    @include('partials.page-js')
