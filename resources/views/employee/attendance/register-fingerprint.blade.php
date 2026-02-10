@php $page = 'employee-fingerprint-register'; @endphp
@extends('layout.mainlayout')
@section('title', 'تسجيل بصمة الإصبع')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                نظام الحضور والانصراف
            @endslot
            @slot('li_1')
                لوحة التحكم
            @endslot
            @slot('li_2')
                تسجيل بصمة الإصبع
            @endslot
        @endcomponent

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4 class="card-title">إعداد بصمة الإصبع</h4>
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted">
                            لتتمكن من تسجيل حضورك وانصرافك باستخدام هاتفك، يرجى تسجيل بصمتك.
                            هذه العملية تتم لمرة واحدة فقط.
                        </p>
                        <i class="fas fa-fingerprint fa-5x text-primary my-4"></i>
                        
                        <div id="status-message" class="mb-3"></div>

                        <button id="register-btn" class="btn btn-primary btn-lg w-100">
                            <span id="btn-text">بدء تسجيل البصمة</span>
                            <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        </button>
                        
                        <div class="alert alert-warning mt-4">
                            <strong>ملاحظة:</strong>
                            تأكد من أنك تستخدم متصفحًا حديثًا (مثل Chrome أو Firefox) على هاتفك الذكي، وأن الموقع مفتوح عبر اتصال آمن (HTTPS).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/biometric-auth.js') }}"></script>
<script src="{{ asset('js/attendance-system.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerBtn = document.getElementById('register-btn');
    const statusMessage = document.getElementById('status-message');
    const btnText = document.getElementById('btn-text');
    const spinner = document.getElementById('spinner');
    
    // URLs for backend communication
    const registrationOptionsUrl = "{{ route('employee.fingerprint.register.options') }}";
    const verificationUrl = "{{ route('employee.fingerprint.register.verify') }}";
    const csrfToken = "{{ csrf_token() }}";

    registerBtn.addEventListener('click', async () => {
        // Disable button and show spinner
        registerBtn.disabled = true;
        btnText.style.display = 'none';
        spinner.style.display = 'inline-block';
        statusMessage.innerHTML = `<div class="alert alert-info">يرجى اتباع التعليمات التي تظهر على شاشتك لوضع إصبعك على المستشعر...</div>`;

        const result = await registerFingerprint(registrationOptionsUrl, verificationUrl, csrfToken);

        // Re-enable button and hide spinner
        registerBtn.disabled = false;
        btnText.style.display = 'inline-block';
        spinner.style.display = 'none';

        if (result.success) {
            statusMessage.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
            registerBtn.style.display = 'none'; // Hide the button on success
            setTimeout(() => {
                window.location.href = "{{ route('employee.attendance.index') }}"; // Redirect to attendance page
            }, 2000);
        } else {
            statusMessage.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    });
});
</script>
@endpush