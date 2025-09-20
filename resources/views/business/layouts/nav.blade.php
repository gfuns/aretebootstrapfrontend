<nav class="navbar-vertical navbar">
    <div class="vh-100" data-simplebar>
        <!-- Brand logo -->
        <a class="navbar-brand" href="{{ route('business.dashboard') }}">
            <h3 class="fw-bold"><img src="{{ asset('images/logo.png') }}" alt="">
            </h3>
        </a>
        <!-- Navbar nav -->
        <ul class="navbar-nav flex-column" id="sideNavbar">
            <li class="nav-item">
                <a class="nav-link " id="dashboard" href="{{ route('business.dashboard') }}">
                    <i class="nav-icon fe fe-home me-2"></i>
                    Dashboard
                </a>
            </li>

            @if (Auth::user()->activePlan() != null)
                <!-- Nav item -->
                <li class="nav-item">
                    <div class="nav-divider"></div>
                </li>

                <li class="nav-item">
                    <a class="nav-link " id="businessInfo" href="{{ route('business.businessProfile') }}">
                        <i class="nav-icon fe fe-briefcase me-2"></i>
                        Business Information
                    </a>
                </li>

                <!-- Nav item -->
                <li class="nav-item">
                    <div class="nav-divider"></div>
                </li>

                <li class="nav-item">
                    <a class="nav-link " id="page" href="{{ route('business.businessPage') }}">
                        <i class="nav-icon fe fe-layout me-2"></i>
                        Business Page Setup
                    </a>
                </li>
            @endif

            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>

            <li class="nav-item">
                <a class="nav-link " id="wallet" href="{{ route('business.myWallet') }}">
                    <i class="nav-icon bi bi-wallet me-2"></i>
                    Wallet
                </a>
            </li>

            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>

            <li class="nav-item">
                <a class="nav-link  collapsed " href="#" data-bs-toggle="collapse"
                    data-bs-target="#navBillPayments" aria-expanded="false" aria-controls="navBillPayments">
                    <i class="nav-icon bi bi-lightbulb-fill me-2"></i> Utilities
                </a>
                <div id="navBillPayments" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">

                        <li class="nav-item">
                            <a class="nav-link " id="airtime" href="{{ route('business.buyAirtime') }}">
                                Airtime Purchase
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " id="data" href="{{ route('business.buyData') }}">
                                Data Purchase
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " id="cable" href="{{ route('business.buyCable') }}">
                                Cable Subscription
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " id="electricity" href="{{ route('business.buyElectricity') }}">
                                Electricity Purchase
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link " id="billTrx" href="{{ route('business.utilityTransactions') }}">
                                Transaction History
                            </a>
                        </li>


                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>

            <li class="nav-item">
                <a class="nav-link " id="forum" href="/forum">
                    <i class="nav-icon bi bi-chat-left-dots me-2"></i>
                    Forum
                </a>
            </li>

            <!-- Nav item -->
            {{-- <li class="nav-item">
                <div class="nav-divider"></div>
            </li>

            <li class="nav-item">
                <a class="nav-link " id="messages" href="#">
                    <i class="nav-icon fe fe-message-square me-2"></i>
                    Messages
                </a>
            </li> --}}


            <!-- Nav item -->
            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>

            <li class="nav-item">
                <a class="nav-link " id="referrals" href="{{ route('business.referrals') }}">
                    <i class="nav-icon bi bi-diagram-3-fill me-2"></i>
                    Referrals
                </a>
            </li>

            @if (Auth::user()->activePlan() != null)
                <!-- Nav item -->
                <li class="nav-item">
                    <div class="nav-divider"></div>
                </li>

                <li class="nav-item">
                    <a class="nav-link " id="jobs" href="{{ route('business.jobListing') }}">
                        <i class="nav-icon bi bi-briefcase-fill me-2"></i>
                        Job Listing
                    </a>
                </li>
            @endif


            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>

            <li class="nav-item">
                <a class="nav-link  collapsed " href="#" data-bs-toggle="collapse" data-bs-target="#navSettings"
                    aria-expanded="false" aria-controls="navSettings">
                    <i class="nav-icon bi bi-gear-wide-connected me-2"></i> Account Settings
                </a>
                <div id="navSettings" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">

                        <li class="nav-item">
                            <a class="nav-link " id="profile" href="{{ route('business.viewProfile') }}">
                                Profile Information
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link " id="subscription" href="{{ route('business.subscription') }}">
                                Billing & Payments
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link " id="notification" href="{{ route('business.notificationSettings') }}">
                                Notifications
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link " id="security" href="{{ route('business.security') }}">
                                Security
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link " id="deleteAccount" href="{{ route('business.deleteAccount') }}">
                                Delete Account
                            </a>
                        </li>

                    </ul>
                </div>
            </li>

            <!-- Nav item -->
            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>


            <li class="nav-item">
                <a class="nav-link " id="help" href="javascript:void(Tawk_API.toggle())">
                    <i class="nav-icon bi bi-question-octagon-fill me-2"></i>
                    Get Help
                </a>
            </li>

            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>

            <li class="nav-item">
                <a class="nav-link " href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                    <i class="nav-icon fe fe-log-out me-2"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
        <!-- Card -->

    </div>
</nav>
