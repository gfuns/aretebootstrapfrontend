<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <title>Sign-In | {{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ asset('auth/assets/css/vendor.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('auth/assets/css/style.css') }}">

    <style type="text/css">
        .password-toggle {
            position: relative;
        }

        .password-toggle input[type="password"] {
            padding-right: 30px;
        }

        .password-toggle .toggle-password {
            position: absolute;
            top: 35%;
            right: 5px;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>

</head>

<body class="page-ath theme-modern page-ath-modern">

    <div class="page-ath-wrap flex-row-reverse">
        <div class="page-ath-content">
            <div class="page-ath-header">
                <a href="/" class="page-ath-logo">
                    <img class="page-ath-logo-img" src="{{ asset('files/general/logo.png') }}"
                        alt="{{ env('APP_NAME') }}">
                </a>
            </div>


            <div class="page-ath-form">
                <h2 class="page-ath-heading">Sign In
                    <small>with your {{ env('APP_NAME') }}
                        account credentials</small>
                </h2>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Invalid Login Credentials</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (Session::has('deletionProcessMessage'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Your Account Is Undergoing Deletion Process</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <form class="validate validate-modern" action="{{ route('login') }}" method="POST">
                    @csrf

                    <x-honeypot />

                    <div class="input-item">
                        <input type="email" placeholder="Your Email" data-msg-required="Required."
                            class="input-bordered" name="email" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="input-item password-toggle">
                        <input id="password" type="password" placeholder="Password" minlength="6"
                            data-msg-required="Required." data-msg-minlength="At least 6 chars." class="input-bordered"
                            name="password" value="" required>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="input-item text-left">
                            <input class="input-checkbox input-checkbox-md" type="checkbox" name="remember"
                                id="remember-me">
                            <label for="remember-me">Remember Me</label>
                        </div>
                        <div>
                            <a href="/password/reset">Forgot password?</a>
                            <div class="gaps-2x"></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </form>

                <div class="gaps-4x"></div>
                <div class="form-note">
                    Don’t have an account? <a href="/register"> <strong>Sign up
                            here</strong></a>
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

                        <p class="text-white">The No. 1 world class cutting-edge business directory designed for
                            businesses
                            like you to elevate your business experience!</p>
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


    <script type="text/javascript">
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var icon = document.querySelector(".toggle-password i");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>

</body>

</html>
