@php $page = 'login'; @endphp
@extends('layout.mainlayout') {{-- Ensure this layout file exists and is correctly set up --}}

@section('content')
    <div class="container-fluid px-0">
        <div class="row">
            <!-- Login logo -->
            <div class="col-lg-6 login-wrap d-none d-lg-flex"> {{-- Added d-none d-lg-flex to hide on small screens and show on large, adjust as needed --}}
                <div class="login-sec">
                    <div class="log-img">
                        <img class="img-fluid" src="{{ URL::asset('/assets/img/login-02.png') }}" alt="Login Visual">
                    </div>
                </div>
            </div>
            <!-- /Login logo -->

            <!-- Login Content -->
            <div class="col-lg-6 login-wrap-bg">
                <div class="login-wrapper">
                    <div class="loginbox">
                        <div class="login-right">
                            <div class="login-right-wrap">
                                <div class="account-logo">
                                    <a href="{{ url('/') }}">
                                        <img src="{{ URL::asset('/assets/img/login-logo.png') }}" alt="Company Logo">
                                    </a>
                                </div>
                                <h2>{{ __('Login') }}</h2>

                                <!-- Session Status -->
                                @if (session('status'))
                                    <div class="alert alert-success mb-3" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <!-- Form -->
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <!-- Email Address -->
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }} <span class="login-danger">*</span></label>
                                        <input id="email" class="form-control @error('email') is-invalid @enderror" 
                                               type="email" name="email" value="{{ old('email') }}" 
                                               required autofocus autocomplete="username">
                                        @error('email')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group">
                                        <label for="password">{{ __('Password') }} <span class="login-danger">*</span></label>
                                        <input id="password" class="form-control pass-input @error('password') is-invalid @enderror" 
                                               type="password" name="password" 
                                               required autocomplete="current-password">
                                        {{-- Assuming your JS handles this toggle. If not, remove or implement --}}
                                        <span class="profile-views feather-eye-off toggle-password"></span> 
                                        @error('password')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="forgotpass">
                                        <!-- Remember Me -->
                                        <div class="remember-me">
                                            <label for="remember_me" class="custom_check mr-2 mb-0 d-inline-flex remember-me">
                                                {{ __('Remember me') }}
                                                <input id="remember_me" type="checkbox" name="remember">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>

                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}">
                                                {{ __('Forgot your password?') }}
                                            </a>
                                        @endif
                                    </div>

                                    <div class="form-group login-btn">
                                        <button class="btn btn-primary btn-block" type="submit">
                                            {{ __('Log in') }}
                                        </button>
                                    </div>
                                </form>
                                <!-- /Form -->

                                <div class="next-sign">
                                    @if (Route::has('register'))
                                        <p class="account-subtitle">
                                            {{ __("Need an account?") }} <a href="{{ route('register') }}">{{ __('Sign Up') }}</a>
                                        </p>
                                    @endif

                                    <!-- Social Login (Optional - keep if you plan to implement) -->
                                    {{--
                                    <div class="social-login">
                                        <a href="javascript:;"><img
                                                src="{{ URL::asset('/assets/img/icons/login-icon-01.svg') }}"
                                                alt="Social Login 1"></a>
                                        <a href="javascript:;"><img
                                                src="{{ URL::asset('/assets/img/icons/login-icon-02.svg') }}"
                                                alt="Social Login 2"></a>
                                        <a href="javascript:;"><img
                                                src="{{ URL::asset('/assets/img/icons/login-icon-03.svg') }}"
                                                alt="Social Login 3"></a>
                                    </div>
                                    --}}
                                    <!-- /Social Login -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Login Content -->
        </div>
    </div>
@endsection

@push('scripts')
{{-- Add any specific JS for this page, e.g., for the password toggle --}}
{{-- Example for a simple password toggle, assuming feather icons are loaded --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('.toggle-password');
        if (togglePassword) {
            togglePassword.addEventListener('click', function (e) {
                const passwordInput = document.getElementById('password');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('feather-eye-off');
                    this.classList.add('feather-eye');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('feather-eye');
                    this.classList.add('feather-eye-off');
                }
            });
        }
    });
</script>
@endpush