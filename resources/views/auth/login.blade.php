@php $page = 'login'; @endphp
@extends('layout.mainlayout') {{-- تأكد من أن ملف التصميم هذا موجود ومُعد بشكل صحيح --}}

@section('content')
    <div class="container-fluid px-0">
        <div class="row">
            <!-- الصورة الجانبية -->
            <div class="col-lg-6 login-wrap d-none d-lg-flex">
                <div class="login-sec">
                    <div class="log-img">
                        <img class="img-fluid" src="{{ URL::asset('/assets/img/login-02.png') }}" alt="صورة تسجيل الدخول">
                    </div>
                </div>
            </div>
            <!-- /الصورة الجانبية -->

            <!-- محتوى تسجيل الدخول -->
            <div class="col-lg-6 login-wrap-bg">
                <div class="login-wrapper">
                    <div class="loginbox">
                        <div class="login-right">
                            <div class="login-right-wrap">
                                {{-- تم استبدال الشعار بنص ترحيبي --}}
                                <div class="account-logo text-center mb-4">
                                    <h3>مرحبا بكم في نظام مركز الاحسان الطبي</h3>
                                </div>

                                <h2>تسجيل الدخول</h2>

                                <!-- حالة الجلسة -->
                                @if (session('status'))
                                    <div class="alert alert-success mb-3" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <!-- نموذج تسجيل الدخول -->
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <!-- البريد الإلكتروني -->
                                    <div class="form-group">
                                        <label for="email">البريد الإلكتروني <span class="login-danger">*</span></label>
                                        <input id="email" class="form-control @error('email') is-invalid @enderror"
                                               type="email" name="email" value="{{ old('email') }}"
                                               required autofocus autocomplete="username">
                                        @error('email')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- كلمة المرور -->
                                    <div class="form-group">
                                        <label for="password">كلمة المرور <span class="login-danger">*</span></label>
                                        <input id="password" class="form-control pass-input @error('password') is-invalid @enderror"
                                               type="password" name="password"
                                               required autocomplete="current-password">
                                        <span class="profile-views feather-eye-off toggle-password"></span>
                                        @error('password')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="forgotpass">
                                        <!-- تذكرني -->
                                        <div class="remember-me">
                                            <label for="remember_me" class="custom_check ms-2 mb-0 d-inline-flex remember-me">
                                                تذكرني
                                                <input id="remember_me" type="checkbox" name="remember">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group login-btn">
                                        <button class="btn btn-primary btn-block" type="submit">
                                            تسجيل الدخول
                                        </button>
                                    </div>
                                </form>
                                <!-- /نموذج تسجيل الدخول -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /محتوى تسجيل الدخول -->
        </div>
    </div>
@endsection

@push('scripts')
{{-- السكريبت الخاص بإظهار وإخفاء كلمة المرور --}}
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