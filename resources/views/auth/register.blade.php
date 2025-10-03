<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <title>Sign-Up | {{ env('APP_NAME') }}</title>
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

        .password-toggle .toggle-password-2 {
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
        <div class="page-ath-content" style="pading:0px; margin:0px">
            <div class="page-ath-header">
                <a href="/" class="page-ath-logo">
                    <img class="page-ath-logo-img" src="{{ asset('files/general/logo.png') }}"
                        alt="{{ env('APP_NAME') }}">
                </a>
            </div>


            <div class="page-ath-form">

                <h2 class="page-ath-heading">Sign Up
                    <small>Create Your {{ env('APP_NAME') }} Account</small>
                </h2>
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <form class="validate validate-modern" method="POST" action="{{ route('register') }}" id="register">
                    @csrf
                    <div class="input-item">
                        <input type="text" placeholder="First Name" class="input-bordered" name="first_name"
                            value="{{ old('first_name') }}" minlength="3" data-msg-required="Required."
                            data-msg-minlength="At least 3 chars." required>
                    </div>
                    <div class="input-item">
                        <input type="text" placeholder="Last Name" class="input-bordered" name="last_name"
                            value="{{ old('last_name') }}" minlength="3" data-msg-required="Required."
                            data-msg-minlength="At least 3 chars." required>
                    </div>
                    <div class="input-item">
                        <input type="email" placeholder="Your Email" class="input-bordered" name="email"
                            value="{{ old('email') }}" data-msg-required="Required."
                            data-msg-email="Enter valid email." required>
                    </div>
                    <div class="input-item password-toggle">
                        <input type="password" placeholder="Password" class="input-bordered" name="password"
                            id="password" minlength="6" data-msg-required="Required."
                            data-msg-minlength="At least 6 chars." required>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                    <div class="input-item password-toggle">
                        <input type="password" placeholder="Repeat Password" class="input-bordered"
                            name="password_confirmation" data-rule-equalTo="#password" id="password2" minlength="6"
                            data-msg-required="Required." data-msg-equalTo="Enter the same value."
                            data-msg-minlength="At least 6 chars." required>
                        <span class="toggle-password-2" onclick="togglePassword2Visibility()">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                    <div class="input-item">
                        <input type="text" placeholder="Referral ID"
                            value="{{ request()->ref == null ? old('referral_code') : request()->ref }}"
                            class="input-bordered" name="referral_code"
                            @if (isset(request()->ref)) readonly @endif>
                    </div>

                    <div class="input-item text-left">
                        <input name="terms" class="input-checkbox input-checkbox-md" id="agree" type="checkbox"
                            required="required" data-msg-required="You should accept our terms and policy.">
                        <label for="agree">I agree to the <a target="_blank" href="/terms-and-conditions">Terms
                                and
                                Condition</a>
                            and <a target="_blank" href="/privacy-policy">Privacy
                                Policy</a>.</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                </form>


                <div class="gaps-2x"></div>
                <div class="form-note">
                    Already have an account ? <a href="/login"> <strong>Sign in
                            instead</strong></a>
                </div>

                {{-- Load the reCAPTCHA script --}}
                {!! NoCaptcha::renderJs() !!}
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
                    <div style="padding-bottom: 50px">
                        <a href="/"><span
                                style="background-color: white; color: #690068; padding:10px; border-radius: 20px"><strong>Back
                                    to Home</strong></span></a>
                    </div>

                    <div style="margin-top: 400px; margin-bottom: 150px">
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

        function togglePassword2Visibility() {
            var passwordInput = document.getElementById("password2");
            var icon = document.querySelector(".toggle-password-2 i");

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
