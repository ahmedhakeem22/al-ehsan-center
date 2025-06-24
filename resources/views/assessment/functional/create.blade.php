@php $page = 'assessment-create'; @endphp
@extends('layout.mainlayout') {{-- أو layout.mainlayout_admin إذا كان لديك قالب مختلف للإدارة --}}
@section('title', 'إضافة تقييم تحسن وظيفي لـ: ' . $patient->full_name)

@push('styles')
{{-- يمكنك إضافة أي CSS مخصص هنا إذا لزم الأمر --}}
<style>
    .btn-check:checked+.btn-outline-secondary {
        background-color: #0d6efd; /* لون Bootstrap primary كمثال */
        color: white;
        border-color: #0d6efd;
    }
    .form-group.row.align-items-center label {
        padding-top: calc(0.375rem + 1px); /* لمحاذاة التسمية مع أزرار الراديو */
    }
</style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid"> {{-- container-fluid لملء الشاشة بشكل أفضل --}}
            @component('components.page-header')
                @slot('title')
                    تقييم التحسن الوظيفي
                @endslot
                @slot('li_1')
                    <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }} ({{$patient->file_number}})</a>
                @endslot
                @slot('li_2')
                    إضافة تقييم جديد
                @endslot
            @endcomponent

            <div class="row justify-content-center"> {{-- توسيط المحتوى --}}
                <div class="col-lg-10 col-xl-8"> {{-- تحديد عرض أقصى للنموذج --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0 text-white">إضافة تقييم تحسن وظيفي للمريض: {{ $patient->full_name }}</h4>
                        </div>
                        <div class="card-body p-4"> {{-- زيادة الـ padding --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>المريض:</strong> {{ $patient->full_name }} ({{ $patient->file_number }})
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <strong>المقيم:</strong> {{ auth()->user()->name }}
                                </div>
                            </div>
                            <hr>
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <p class="fw-bold">يرجى تصحيح الأخطاء التالية:</p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('assessment.functional.store', $patient->id) }}" method="POST">
                                @include('assessment.functional._form', [
                                    'assessmentItems' => $assessmentItems,
                                    'currentGregorianDate' => $currentGregorianDate,
                                    'currentHijriDate' => $currentHijriDate
                                ])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- @component('components.notification-box') @endcomponent --}}
    </div>
@endsection

@push('scripts')
{{-- تأكد من تضمين jQuery ومكتبة DateTime Picker إذا كنت تستخدمها --}}
{{-- مثال لتضمين مكتبة Tempus Dominus (Bootstrap 5) إذا كانت هي المستخدمة في القالب --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/tempus-dominus@6.7.11/dist/js/tempus-dominus.min.js" integrity="sha384-qCROQyEXIF2uP+nUmDbr37CHyF4WHRlfQgzIibA57WUUx3bTQsHkSdG8D5R2i2xQ" crossorigin="anonymous"></script> --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempus-dominus@6.7.11/dist/css/tempus-dominus.min.css" integrity="sha384-96xG2lnjMhKCXD6fI9lO0jU8s1MPL3x8k73/iB5fRtkjNfC4z2WqIu7z/L9IGh7l" crossorigin="anonymous"> --}}

<script>
$(document).ready(function() {
    // تهيئة أي مكتبة datetimepicker تستخدمها.
    // الكود التالي يفترض أنك تستخدم مكتبة Bootstrap Datetimepicker (التي تعتمد على Moment.js)
    // أو مكتبة مشابهة تقبل هذا التنسيق.
    if($('.datetimepicker').length > 0) {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD', // هذا هو التنسيق الذي يجب أن يرسله الـ picker
            useCurrent: false,    // يمنع التعيين التلقائي للوقت الحالي عند الفتح
            // sideBySide: true, // إذا كنت تريد عرض الوقت بجانب التاريخ (غير مطلوب هنا)
            // locale: 'ar-sa', // أو 'ar' إذا كانت المكتبة تدعمها لأسماء الشهور والأيام بالعربية
            // icons: { // يمكنك تخصيص الأيقونات إذا أردت
            //     time: "fa fa-clock-o",
            //     date: "fa fa-calendar",
            //     up: "fa fa-chevron-up",
            //     down: "fa fa-chevron-down",
            //     previous: 'fa fa-chevron-left',
            //     next: 'fa fa-chevron-right',
            //     today: 'fa fa-crosshairs',
            //     clear: 'fa fa-trash',
            //     close: 'fa fa-times'
            // }
        }).on('dp.change', function(e) {
            // يمكنك إضافة أي منطق هنا عند تغيير التاريخ إذا لزم الأمر
            // console.log('Date changed: ', e.date ? e.date.format('YYYY-MM-DD') : 'No date');
        });

        // إذا كنت تريد أن يظهر الـ picker عند النقر على الأيقونة أيضًا (إذا كان لديك أيقونة بجانب الحقل)
        // $('.input-group-addon').click(function(){
        //     $(this).closest('.input-group.date').find('.datetimepicker').data("DateTimePicker").show();
        // });
    }
});
</script>
@endpush