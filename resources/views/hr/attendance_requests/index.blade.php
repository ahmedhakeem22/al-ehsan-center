@extends('layout.mainlayout')
@section('title', 'طلبات التحضير اليدوي')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title') طلبات التحضير @endslot
            @slot('li_1') الموارد البشرية @endslot
            @slot('li_2') طلبات التحضير @endslot
        @endcomponent

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th>نوع الطلب</th>
                                        <th>وقت الطلب</th>
                                        <th class="text-end">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($requests as $request)
                                        <tr>
                                            <td>{{ $request->employee->full_name }}</td>
                                            <td>
                                                @if($request->request_type == 'check_in')
                                                    <span class="badge bg-success">تسجيل حضور</span>
                                                @else
                                                    <span class="badge bg-warning">تسجيل انصراف</span>
                                                @endif
                                            </td>
                                            <td>{{ $request->created_at->format('h:i A') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('hr.attendance-requests.generate-qr', $request->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="fas fa-qrcode"></i> عرض QR Code
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center">لا توجد طلبات معلقة حالياً.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection