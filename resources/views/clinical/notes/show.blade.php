@extends('layout.mainlayout')
@section('title', 'عرض ملاحظة سريرية #' . $note->id)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') عرض ملاحظة سريرية @endslot
            @slot('li_1') <a href="{{ route('clinical.notes.index', $patient->id) }}">قائمة الملاحظات</a> @endslot
            @slot('li_2') ملاحظة رقم #{{ $note->id }} @endslot
        @endcomponent

        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('clinical.notes.index', $patient->id) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right me-1"></i> العودة لقائمة الملاحظات</a>
            </div>
            @if($note->author_id === auth()->id())
            <div class="col-md-6 text-end">
                <a href="{{ route('clinical.notes.edit', [$patient->id, $note->id]) }}" class="btn btn-warning"><i class="fas fa-pen me-1"></i> تعديل</a>
                <form action="{{ route('clinical.notes.destroy', [$patient->id, $note->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الملاحظة؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt me-1"></i> حذف</button>
                </form>
            </div>
            @endif
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ Str::ucfirst(str_replace('_', ' ', $note->note_type)) }}</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4"><strong>الكاتب:</strong><p class="text-muted">{{ $note->author->name }} ({{ Str::ucfirst($note->author_role) }})</p></div>
                    <div class="col-md-4"><strong>التاريخ:</strong><p class="text-muted">{{ $note->created_at->format('Y-m-d, h:i A') }}</p></div>
                    <div class="col-md-4"><strong>المريض:</strong><p class="text-muted">{{ $patient->full_name }}</p></div>
                </div>
                <hr>
                <h5>المحتوى</h5>
                <div class="p-3 bg-light rounded">
                    <p>{!! nl2br(e($note->content)) !!}</p>
                </div>
            </div>
        </div>

        {{-- Action Section --}}
        @if($note->is_actioned)
            <div class="card border-success">
                <div class="card-header bg-success-light text-success">
                    <h5 class="card-title mb-0"><i class="fas fa-check-circle me-2"></i> تم تنفيذ هذه التوصية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><strong>تم التنفيذ بواسطة:</strong><p class="text-muted">{{ $note->actionedBy->name }}</p></div>
                        <div class="col-md-6"><strong>تاريخ التنفيذ:</strong><p class="text-muted">{{ $note->actioned_at->format('Y-m-d, h:i A') }}</p></div>
                    </div>
                    @if($note->action_notes)
                        <strong>ملاحظات التنفيذ:</strong>
                        <p class="text-muted fst-italic">{{ $note->action_notes }}</p>
                    @endif
                </div>
            </div>
        @elseif($note->note_type === 'doctor_recommendation' && in_array(auth()->user()->role->name, ['Nurse', 'Admin']))
            <div class="card border-warning">
                <div class="card-header bg-warning-light text-dark">
                    <h5 class="card-title mb-0"><i class="fas fa-tasks me-2"></i> تنفيذ الإجراء</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('clinical.notes.action', [$patient->id, $note->id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="action_notes" class="form-label">ملاحظات على التنفيذ (اختياري)</label>
                            <textarea name="action_notes" id="action_notes" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="confirmAction" required>
                            <label class="form-check-label" for="confirmAction">
                                أؤكد أنني قمت بتنفيذ هذه التوصية.
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success">تأكيد التنفيذ</button>
                    </form>
                </div>
            </div>
        @endif
        
        {{-- Replies can be shown here --}}

    </div>
</div>
@endsection