@extends('layout.mainlayout')
@section('title', 'الملاحظات السريرية - ' . $patient->full_name)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') الملف السريري للمريض: {{ $patient->full_name }} @endslot
            @slot('li_1') <a href="{{ route('patient_management.patients.show', $patient->id) }}">ملف المريض</a> @endslot
            @slot('li_2') الملاحظات السريرية @endslot
        @endcomponent

        <div class="row mb-3">
            <div class="col">
                <a href="{{ route('patient_management.patients.show', $patient->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> العودة لملف المريض
                </a>
            </div>
            <div class="col text-end">
                <a href="{{ route('clinical.notes.create', $patient->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة ملاحظة جديدة
                </a>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-filter me-2"></i> فلترة الملاحظات</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('clinical.notes.index', $patient->id) }}" method="GET">
                    <div class="row align-items-end">
                      <div class="col-md-5">
    <label for="note_type" class="form-label">نوع الملاحظة</label>
    <select name="note_type" id="note_type" class="form-select">
        <option value="">جميع الأنواع</option>
        {{-- Here, $noteTypes is an array of Enum cases --}}
        @foreach($noteTypes as $type)
            {{-- We use $type->value for the value attribute (e.g., 'doctor_recommendation') --}}
            {{-- We use $type->label() for the display text (e.g., 'توصية طبيب') --}}
            <option value="{{ $type->value }}" @selected(request('note_type') == $type->value)>
                {{ $type->label() }}
            </option>
        @endforeach
    </select>
</div>
                        <div class="col-md-5">
                            <label for="author_role" class="form-label">دور الكاتب</label>
                            <select name="author_role" id="author_role" class="form-select">
                                <option value="">جميع الأدوار</option>
                                @foreach($authorRoles as $role)
                                    <option value="{{ $role }}" @selected(request('author_role') == $role)>{{ Str::ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex mt-4">
                            <button type="submit" class="btn btn-primary flex-grow-1 me-2"><i class="fas fa-search"></i></button>
                            <a href="{{ route('clinical.notes.index', $patient->id) }}" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-redo"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{-- /Filter Card --}}

        <div class="row mt-4">
            <div class="col-md-12">
                @if($notes->count() > 0)
                    <div class="timeline-group">
                        @foreach ($notes as $note)
                            <div class="timeline-item">
                                <div class="timeline-item-marker">
                                    <div class="timeline-item-marker-indicator bg-primary"></div>
                                </div>
                                <div class="timeline-item-content">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <a href="{{ route('clinical.notes.show', [$patient->id, $note->id]) }}" class="fw-bold text-dark">{{ $note->note_type->label() }}</a>
                                                    <p class="mb-0 text-muted">
                                                        بواسطة: {{ $note->author->name }} ({{ Str::ucfirst($note->author_role) }})
                                                    </p>
                                                </div>
                                                <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mt-2">{{ Str::limit($note->content, 200) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @if($note->is_actioned)
                                                        <span class="badge bg-success-light"><i class="fas fa-check-circle me-1"></i> تم التنفيذ</span>
                                                    @elseif($note->note_type === 'doctor_recommendation')
                                                        <span class="badge bg-warning-light"><i class="fas fa-hourglass-half me-1"></i> بانتظار التنفيذ</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('clinical.notes.show', [$patient->id, $note->id]) }}" class="btn btn-sm btn-outline-primary">عرض التفاصيل</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($notes->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $notes->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h4>لا توجد ملاحظات سريرية</h4>
                        <p class="text-muted">لم يتم العثور على ملاحظات تطابق معايير البحث الحالية.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection