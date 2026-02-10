@php $page = 'employee-attendance'; @endphp
@extends('layout.mainlayout')
@section('title', 'تسجيل الحضور والانصراف')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                الحضور والانصراف
            @endslot
            @slot('li_1')
                لوحة التحكم
            @endslot
            @slot('li_2')
                تسجيل الحضور
            @endslot
        @endcomponent

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card text-center">
                    <div class="card-header">
                        <h4 class="card-title">اليوم: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h4>
                    </div>
                    <div class="card-body">
                        <div id="status-message" class="mb-3">
                            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
                            @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
                        </div>

                        @if ($status === 'not_checked_in')
                            <i class="fas fa-sign-in-alt fa-5x text-success my-4"></i>
                            <p class="lead">أنت لم تسجل حضورك بعد.</p>
                            <button id="check-in-btn" class="btn btn-success btn-lg w-100">تسجيل الحضور</button>
                        @elseif ($status === 'checked_in')
                            <i class="fas fa-sign-out-alt fa-5x text-danger my-4"></i>
                            <p class="lead">تم تسجيل حضورك في الساعة: <strong>{{ $record->check_in_time->format('h:i:s A') }}</strong></p>
                            <button id="check-out-btn" class="btn btn-danger btn-lg w-100">تسجيل الانصراف</button>
                        @elseif ($status === 'checked_out')
                            <i class="fas fa-check-circle fa-5x text-info my-4"></i>
                            <p class="lead">لقد أكملت دوامك لهذا اليوم.</p>
                            <div class="alert alert-info">
                                وقت الحضور: <strong>{{ $record->check_in_time->format('h:i:s A') }}</strong>
                                <br>
                                وقت الانصراف: <strong>{{ $record->check_out_time->format('h:i:s A') }}</strong>
                            </div>
                        @endif
                        
                        <div id="action-spinner" class="mt-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>جاري التحقق من البصمة...</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('employee.attendance.history') }}">عرض سجل الحضور الخاص بي</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/biometric-auth.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkInBtn = document.getElementById('check-in-btn');
    const checkOutBtn = document.getElementById('check-out-btn');
    const statusMessage = document.getElementById('status-message');
    const actionSpinner = document.getElementById('action-spinner');
    
    const authOptionsUrl = "{{ route('employee.fingerprint.auth.options') }}";
    const checkInUrl = "{{ route('employee.attendance.checkin') }}";
    const checkOutUrl = "{{ route('employee.attendance.checkout') }}";
    const csrfToken = "{{ csrf_token() }}";

    async function handleAttendanceAction(actionUrl, button) {
        button.style.display = 'none';
        actionSpinner.style.display = 'block';
        statusMessage.innerHTML = '';

        const result = await authenticateWithFingerprint(authOptionsUrl, actionUrl, csrfToken);

        if (result.success) {
            statusMessage.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
            // Reload the page to show the new status
            setTimeout(() => window.location.reload(), 1500);
        } else {
            statusMessage.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
            button.style.display = 'block'; // Show button again on failure
            actionSpinner.style.display = 'none';
        }
    }

    if (checkInBtn) {
        checkInBtn.addEventListener('click', () => handleAttendanceAction(checkInUrl, checkInBtn));
    }
    
    if (checkOutBtn) {
        checkOutBtn.addEventListener('click', () => handleAttendanceAction(checkOutUrl, checkOutBtn));
    }
});
</script>
@endpush