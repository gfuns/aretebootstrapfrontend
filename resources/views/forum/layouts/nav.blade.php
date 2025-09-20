<!--========================== Header section Start ==========================-->
<div class="header-main-area">
    <div class="header" id="header">
        <div class="container-fluid position-relative">
            <div class="row">
                <div class="header-wrapper">
                    <!-- ham menu -->
                    <i class="fa-sharp fa-solid fa-bars-staggered ham__menu" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"></i>

                    <!-- logo -->
                    <div class="header-menu-wrapper align-items-center d-flex">
                        <div class="logo-wrapper">
                            <a href="/" class="normal-logo" id="normal-logo">
                                <img src="{{ asset('files/general/logo.png') }}" alt="">
                            </a>
                            <a href="{{ route('forum') }}" class="dark-logo hidden" id="dark-logo">
                                <img src="{{ asset('files/general/logo.png') }}" alt="">
                            </a>
                        </div>
                    </div>
                    <!-- / logo -->

                    <!-- Header Menu -->
                    <div class="menu-wrapper">
                        <ul class="main-menu">
                            <li>
                                <div class="header-search-bar">
                                    <form id="header-search">
                                        <div class="header-search-input">
                                            <input type="text" placeholder="" class="header-form--control"
                                                onkeyup="search(this);">
                                        </div>
                                        <button class="header-search-btn">
                                            <span class="icon">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                                <div class="search-result-box d-none">

                                </div>
                            </li>
                        </ul>
                    </div>
                    <!-- Header Menu /> -->
                    <!-- user actn -->
                    @if (Auth::user())
                        <div class="menu-right-wrapper">


                            <ul>
                                <li class="search-icon">
                                    <a href="#" class="login-registration-list__link">
                                        <span class="icon search-icon">
                                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                        </span>
                                    </a>
                                </li>

                                <!-- < dark option -->
                                <li class="dark-mood-option">
                                    <div class="light-dark-btn-wrap ms-1" id="light-dark-checkbox1">
                                        <i class="las la-moon mon-icon"></i>
                                        <i class="las la-sun sun-icon "></i>
                                    </div>
                                </li>
                                <!-- dark option /> -->

                                <li class="language-box">
                                    <div class="avatar-thumb">
                                        <a href="/forum/user/{{ Auth::user()->id }}">
                                            <img src="{{ isset(Auth::user()->photo) ? Auth::user()->photo : asset('proforum/images/avatar.png') }}"
                                                alt="avatar">
                                        </a>
                                    </div>

                                    {{-- <i class="fa-solid fa-globe"></i> --}}
                                    <nav class="site-navigation">
                                        <div class="site-menu">
                                            <div class="site-menu-item has-dropdown">
                                                <div class="tit">
                                                    <a href="#" style="font-weight: bold;">Hi,
                                                        {{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</a>
                                                </div>
                                                <div class="sub-menu">
                                                    <div class="sub-menu-item firstchild"><a
                                                            href="{{ route('business.dashboard') }}">Dashboard</a>
                                                    </div>
                                                    <div class="sub-menu-item otherchild"><a
                                                            href="{{ route('business.viewProfile') }}">Account
                                                            Settings</a></div>
                                                    <div class="sub-menu-item otherchild"><a
                                                            href="{{ route('business.businessProfile') }}">Business
                                                            Settings</a></div>
                                                    <div class="sub-menu-item lastchild"><a
                                                            href="{{ route('logout') }}"
                                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                                        <form id="logout-form" action="{{ route('logout') }}"
                                                            method="POST" style="display: none;">
                                                            {{ csrf_field() }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </nav>
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="menu-right-wrapper">

                            <ul>
                                <li class="search-icon">
                                    <a href="#" class="login-registration-list__link">
                                        <span class="icon search-icon">
                                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                        </span>
                                    </a>
                                </li>

                                <!-- < dark option -->
                                <li class="dark-mood-option">
                                    <div class="light-dark-btn-wrap ms-1" id="light-dark-checkbox1">
                                        <i class="las la-moon mon-icon"></i>
                                        <i class="las la-sun sun-icon"></i>
                                    </div>
                                </li>
                                <!-- dark option /> -->
                                <li class="login-registration-list login-icon">
                                    <a href="/forum/user" class="login-registration-list__link">
                                        <span class="icon">
                                            <i class="las la-user-plus mt-1"></i>
                                        </span>
                                    </a>
                                </li>

                                <li class="language-box">
                                    <i class="fa-solid fa-globe"></i>
                                    <div>
                                        <select class="select langSel">
                                            <option value="en" selected="">
                                                En
                                            </option>
                                        </select>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @endif
                    <!-- user actn /> -->
                </div>

            </div>
        </div>
    </div>
</div>
<!--========================== Header section End ==========================-->



<!--========================== Sidebar mobile menu wrap Start ==========================-->
<div class="offcanvas offcanvas-start text-bg-light" tabindex="-1" id="offcanvasExample">
    <div class="offcanvas-header">
        <div class="logo">
            <div class="header-menu-wrapper align-items-center d-flex">
                <div class="logo-wrapper">
                    <a href="{{ route('forum') }}" class="normal-logo" id="offcanvas-logo-normal"> <img
                            src="{{ asset('files/general/logo.png') }}" alt=""></a>
                    <a href="{{ route('forum') }}" class="dark-logo hidden" id="offcanvas-logo-dark"> <img
                            src="{{ asset('files/general/logo.png') }}" alt=""></a>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="side-Nav">
            <li>
                <a class="active" href="{{ route('forum') }}">
                    Home </a>
            </li>

            <li>
                <a href="{{ route('forum.popularPosts') }}">
                    Popular </a>
            </li>

            <li>
                <a href="{{ route('forum.bookmarks') }}">
                    Bookmarks </a>
            </li>
            <p class="pt-4 pb-2 gfuns"><strong>Categories</strong></p>
            @foreach ($forumCategories as $fc)
                <li>
                    <a href="/forum/category/{{ $fc->id }}/posts">
                        {{ $fc->category }} </a>
                </li>
            @endforeach

            <p class="pt-4 pb-2 gfuns"><strong>Topics</strong></p>
            @foreach ($topTopics as $ft)
                <li>
                    <a href="/forum/topic/{{ $ft->id }}/posts">
                        {{ $ft->topic }} </a>
                </li>
            @endforeach

            @foreach ($otherTopics as $oft)
                <li>
                    <a href="/forum/topic/{{ $oft->id }}/posts">
                        {{ $oft->topic }} </a>
                </li>
            @endforeach

            <p class="pt-4 pb-2 gfuns"><strong>Your Account</strong></p>

            @if (Auth::user())
                <li>
                    <a href="{{ route('business.dashboard') }}">Dashboard </a>
                </li>

                <li>
                    <a href="{{ route('business.viewProfile') }}">Account
                        Settings </a>
                </li>

                <li>
                    <a href="{{ route('business.businessProfile') }}">Business
                        Settings </a>
                </li>

                <li>
                    <a href="href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }} </a>
                </li>
            @else
                <li>
                    <a href="/register">Sign Up </a>
                </li>
                <li>
                    <a href="/forum/user">Sign In </a>
                </li>
            @endif
            {{-- <li>
                <a href="https://preview.wstacks.com/proforum/user/login">
                    Login </a>
            </li>
            <li>
                <a href="https://preview.wstacks.com/proforum/user/register">
                    Signup </a>
            </li> --}}
            {{-- <li>
                <a href="/forum/cookie-policy">
                    Cookie </a>
            </li>
            <li>
            </li>
            <li>
                <a href="/forum/policy/terms-of-service">
                    Terms of Service
                </a>
            </li>
            <li>
                <a href="/forum/policy/privacy-policy">
                    Privacy Policy
                </a>
            </li> --}}


        </ul>
    </div>
</div>
<!--========================== Sidebar mobile menu wrap End ==========================-->
