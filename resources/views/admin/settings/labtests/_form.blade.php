@csrf
<div class="row">
    {{-- القسم الأيمن: المعلومات الأساسية --}}
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0 text-primary">المعلومات الأساسية للفحص</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                   placeholder="اسم الفحص" value="{{ old('name', $labtest->name ?? '') }}" required>
                            <label for="name">اسم الفحص <span class="text-danger">*</span></label>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
                                   placeholder="كود الفحص" value="{{ old('code', $labtest->code ?? '') }}">
                            <label for="code">كود الفحص (اختياري)</label>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                      placeholder="وصف الفحص" style="height: 100px">{{ old('description', $labtest->description ?? '') }}</textarea>
                            <label for="description">الوصف (اختياري)</label>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0 text-primary">التفاصيل الإضافية</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('reference_range') is-invalid @enderror" id="reference_range" name="reference_range"
                                   placeholder="النطاق المرجعي" value="{{ old('reference_range', $labtest->reference_range ?? '') }}">
                            <label for="reference_range">النطاق المرجعي (اختياري)</label>
                            @error('reference_range')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror" id="cost" name="cost"
                                   placeholder="التكلفة" value="{{ old('cost', $labtest->cost ?? '') }}" min="0">
                            <label for="cost">التكلفة (اختياري)</label>
                            @error('cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- القسم الأيسر: الإجراءات ومعاينة (إذا لزم الأمر) --}}
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0 text-primary">الإجراءات</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">يرجى التأكد من صحة جميع البيانات قبل الحفظ.</p>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg submit-form">
                        <i class="fas fa-save me-2"></i>
                        {{ isset($labtest) ? 'تحديث الفحص' : 'إنشاء الفحص' }}
                    </button>
                    <a href="{{ route('admin.settings.labtests.index') }}" class="btn btn-outline-secondary btn-lg cancel-form">
                        <i class="fas fa-times-circle me-2"></i>
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
        {{-- يمكنك إضافة بطاقة أخرى هنا لمعاينة أو معلومات إضافية --}}
    </div>
</div>