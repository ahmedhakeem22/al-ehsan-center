@php $page = 'assessment-show'; @endphp
@extends('layout.mainlayout')
@section('title', 'تفاصيل تقييم لـ: ' . $patient->full_name)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    تفاصيل تقييم التحسن الوظيفي
                @endslot
                @slot('li_1')
                    <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }} ({{$patient->file_number}})</a>
                @endslot
                @slot('li_2')
                    <a href="{{ route('assessment.functional.index', $patient->id) }}">قائمة التقييمات</a>
                @endslot
                @slot('li_3')
                    تقييم بتاريخ: {{ $assessment->assessment_date_gregorian->format('Y-m-d') }}
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تفاصيل التقييم للمريض: {{ $patient->full_name }}</h4>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="mb-0"><strong>تاريخ التقييم الميلادي:</strong> {{ $assessment->assessment_date_gregorian->format('d M Y') }}</p>
                                    <p class="mb-0"><strong>تاريخ التقييم الهجري:</strong> {{ $assessment->assessment_date_hijri }}</p>
                                    <p class="mb-0"><strong>المقيم:</strong> {{ $assessment->assessor->name ?? 'غير محدد' }}</p>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('assessment.functional.edit', [$patient->id, $assessment->id]) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> تعديل التقييم</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">ملخص النتائج</h5>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    متوسط محور الأدوية:
                                    <span class="badge bg-primary rounded-pill">{{ number_format($assessment->medication_axis_average, 2) ?: 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    متوسط محور التدخلات النفسية:
                                    <span class="badge bg-success rounded-pill">{{ number_format($assessment->psychological_axis_average, 2) ?: 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    متوسط محور الأنشطة:
                                    <span class="badge bg-info rounded-pill">{{ number_format($assessment->activities_axis_average, 2) ?: 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>نسبة التحسن الإجمالية:</strong>
                                    <span class="badge bg-purple rounded-pill fs-6">{{ number_format($assessment->overall_improvement_percentage, 2) }}%</span>
                                </li>
                                <li class="list-group-item">
                                    <strong>مدة البقاء الموصى بها:</strong> {{ $assessment->recommended_stay_duration ?: 'لم تحدد' }}
                                </li>
                            </ul>

                            <h5 class="card-title mt-4">الردود التفصيلية للبنود</h5>
                            @foreach ($responsesGroupedByAxis as $axisType => $responses)
                                <div class="mb-4">
                                    <h6>
                                        @if ($axisType == 'medication')
                                            محور الأدوية النفسية
                                        @elseif ($axisType == 'psychological')
                                            محور التدخلات النفسية
                                        @elseif ($axisType == 'activities')
                                            محور الأنشطة الرياضية والترفيهية
                                        @else
                                            {{ ucfirst($axisType) }}
                                        @endif
                                    </h6>
                                    <ul class="list-unstyled">
                                        @foreach ($responses as $response)
                                            <li class="mb-1 ps-3">
                                                <strong>{{ $response->item->item_text_ar }}:</strong>
                                                <span class="badge bg-secondary">{{ $response->rating }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach

                            @if($assessment->notes)
                                <h5 class="card-title mt-4">ملاحظات إضافية</h5>
                                <p>{{ $assessment->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">مخطط تطور التحسن</h5>
                        </div>
                        <div class="card-body">
                            @if($assessmentHistory->count() > 1)
                                <canvas id="improvementTrendChart"></canvas>
                            @else
                                <p>يلزم تقييمين على الأقل لعرض مخطط التطور.</p>
                            @endif
                        </div>
                    </div>
                     <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">سجل التقييمات</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($patient->assessments()->orderBy('assessment_date_gregorian', 'desc')->take(10)->get() as $histAssessment)
                                <a href="{{ route('assessment.functional.show', [$patient->id, $histAssessment->id]) }}" class="list-group-item list-group-item-action {{ $histAssessment->id == $assessment->id ? 'active' : '' }}">
                                    {{ $histAssessment->assessment_date_gregorian->format('Y-m-d') }} - {{ number_format($histAssessment->overall_improvement_percentage, 1) }}%
                                </a>
                                @empty
                                <li class="list-group-item">لا يوجد سجل</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection

@push('scripts')
@if($assessmentHistory->count() > 1)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('improvementTrendChart');
            const historyData = @json($assessmentHistory->map(function($item) {
                return [
                    \Carbon\Carbon::parse($item->assessment_date_gregorian)->format('Y-m-d'), // أو 'd M'
                    $item->overall_improvement_percentage
                ];
            }));

            const labels = historyData.map(item => item[0]);
            const dataPoints = historyData.map(item => item[1]);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'نسبة التحسن الإجمالية (%)',
                        data: dataPoints,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%'
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endif
@endpush