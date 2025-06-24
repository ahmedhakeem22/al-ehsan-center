@php $page = 'assessment-index'; @endphp
@extends('layout.mainlayout')
@section('title', 'تقييمات التحسن لـ: ' . $patient->full_name)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    تقييمات التحسن الوظيفي
                @endslot
                @slot('li_1')
                    <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }} ({{$patient->file_number}})</a>
                @endslot
                @slot('li_2')
                    قائمة التقييمات
                @endslot
            @endcomponent

            <div class="row mb-3">
                <div class="col-md-6">
                    <h4>التقييمات المسجلة للمريض: {{ $patient->full_name }}</h4>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('assessment.functional.create', $patient->id) }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> إضافة تقييم جديد
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire">
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0 datatable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>تاريخ التقييم (ميلادي)</th>
                                            <th>تاريخ التقييم (هجري)</th>
                                            <th>المقيم</th>
                                            <th>نسبة التحسن الإجمالية (%)</th>
                                            <th class="text-end">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($assessments as $assessment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $assessment->assessment_date_gregorian->format('Y-m-d') }}</td>
                                                <td>{{ $assessment->assessment_date_hijri }}</td>
                                                <td>{{ $assessment->assessor->name ?? 'غير محدد' }}</td>
                                                <td>{{ number_format($assessment->overall_improvement_percentage, 2) }}%</td>
                                                <td class="text-end">
                                                    <a href="{{ route('assessment.functional.show', [$patient->id, $assessment->id]) }}" class="btn btn-sm btn-outline-info me-1">
                                                        <i class="fa fa-eye"></i> عرض
                                                    </a>
                                                    <a href="{{ route('assessment.functional.edit', [$patient->id, $assessment->id]) }}" class="btn btn-sm btn-outline-warning me-1">
                                                        <i class="fa fa-edit"></i> تعديل
                                                    </a>
                                                    <form action="{{ route('assessment.functional.destroy', [$patient->id, $assessment->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fa fa-trash"></i> حذف
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">لا توجد تقييمات مسجلة لهذا المريض حتى الآن.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $assessments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection