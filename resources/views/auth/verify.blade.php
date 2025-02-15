<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <title>Email Verification | {{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ asset('auth/assets/css/vendor.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('auth/assets/css/style.css') }}">
    <style>
        /* Style to make each box look like a digit */
        .verification-box {
            width: 60px;
            height: 60px;
            text-align: center;
            font-size: 20px;
            margin: 0 15px;
            color: black;
            border: 1px solid #ccc;
        }

        @media (max-width: 767px) {
            .verification-box {
                width: 55px;
                height: 55px;
                text-align: center;
                font-size: 18px;
                margin: 0 12px;
                color: black;
                border: 1px solid #ccc;
            }
        }

        @media (max-width: 560px) {
            .verification-box {
                width: 50px;
                height: 50px;
                text-align: center;
                font-size: 18px;
                margin: 0 10px;
                color: black;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>

<body class="page-ath theme-modern page-ath-modern">

    <div class="page-ath-wrap flex-row-reverse">
        <div class="page-ath-content">
            <div class="page-ath-header text-center">
                <a href="/" class="page-ath-logo">
                    <img class="page-ath-logo-img" src="{{ asset('files/general/logo.png') }}"
                        alt="{{ env('APP_NAME') }}">
                </a>
            </div>


            <div class="page-ath-form">
                <h2 class="page-ath-heading text-center">Verify Email
                    <small style="font-size: 16px; line-height: 25px">A verification code was sent to your email
                        <strong>{{ Auth::user()->email }}</strong>. Please input the code to verify your Email</small>
                </h2>
                <form class="validate" action="{{ route('verifyEmail') }}" method="POST">
                    @csrf
                    <div class="input-item">
                        <input type="text" name="digit_1" class="verification-box input-bordered" maxlength="1"
                            id="digit1" oninput="moveToNext(this)">
                        <input type="text" name="digit_2" class="verification-box input-bordered" maxlength="1"
                            id="digit2" oninput="moveToNext(this)">
                        <input type="text" name="digit_3" class="verification-box input-bordered" maxlength="1"
                            id="digit3" oninput="moveToNext(this)">
                        <input type="text" name="digit_4" class="verification-box input-bordered" maxlength="1"
                            id="digit4">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Verify Email</button>
                </form>

                <div class="gaps-4x"></div>

                <div class="form-note">
                    If you did not get the email please <a href="{{ route("sendVerificationMail") }}"><b>Request Another</b></a>.
                </div>

                <div class="gaps-4x"></div>
                <div class="form-note">
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                        <em class="fas fa-arrow-alt-circle-left"></em> <strong>Sign Out</strong>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>


            <div class="page-ath-footer">
                <ul class="socials mb-3">
                    <li><a href="#"><em class="fab fa-facebook-f"></em></a></li>
                    <li><a href="#"><em class="fab fa-twitter"></em></a></li>
                    <li><a href="#"><em class="fab fa-linkedin-in"></em></a></li>
                    <li><a href="#"><em class="fab fa-github-alt"></em></a></li>
                    <li><a href="#"><em class="fab fa-youtube"></em></a></li>
                    <li><a href="#"><em class="fab fa-medium-m"></em></a></li>
                    <li><a href="#"><em class="fab fa-telegram-plane"></em></a></li>
                </ul>
                <ul class="footer-links guttar-20px align-items-center">
                    <li><a href="/privacy-policy" target="_blank">Privacy Policy</a></li>
                    <li><a href="/terms-and-conditions" target="_blank">Terms and Condition</a></li>
                    <li>
                        <div class="lang-switch relative"><a href="javascript:void(0)"
                                class="lang-switch-btn toggle-tigger">EN<em class="ti ti-angle-up"></em></a>
                            <div class="toggle-class dropdown-content dropdown-content-up">
                                <ul class="lang-list">
                                    <li><a href="?lang=en">English</a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="copyright-text">&copy; {{ date('Y') }} {{ env('APP_NAME') }}. All Right Reserved.
                </div>
            </div>
        </div>
        <div class="page-ath-gfx" style="background-image: url({{ asset('auth/images/ath-gfx.png') }});">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-md-11 col-xl-11">
                    <div style="padding-bottom: 30px">
                        <a href="/"><span
                                style="background-color: white; color: #690068; padding:10px; border-radius: 20px"><strong>Back
                                    to Home</strong></span></a>
                    </div>

                    <div style="margin-top: 450px; margin-bottom: 50px">
                        <span style="color:white; font-size: 72px; font-weight:bolder">Welcome to</span>
                        <span style="color:#FEBA00; font-size: 72px; font-weight:bolder"> &nbsp;Arete</span>

                        <p class="text-white">The No. 1 world class cutting-edge job portal designed for professionals like you to elevate your job search experience!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('auth/assets/js/jquery.bundle.js') }}"></script>
    <script src="{{ asset('auth/assets/js/script.js') }}"></script>
    @include('sweetalert::alert')

    <script type="text/javascript">
        jQuery(function() {
            var $frv = jQuery('.validate');
            if ($frv.length > 0) {
                $frv.validate({
                    errorClass: "input-bordered-error error"
                });
            }
        });
    </script>

    <script>
        function moveToNext(currentInput) {
            // Automatically move to the next input box when a digit is entered
            const maxLength = parseInt(currentInput.getAttribute('maxlength'), 10);
            const currentLength = currentInput.value.length;

            if (currentLength >= maxLength) {
                const nextSibling = currentInput.nextElementSibling;

                if (nextSibling && nextSibling.tagName.toLowerCase() === 'input') {
                    nextSibling.focus();
                }
            }
        }
    </script>

</body>

</html>
