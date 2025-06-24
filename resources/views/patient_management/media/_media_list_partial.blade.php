@if($mediaItems->count() > 0)
<div class="row">
    @foreach ($mediaItems as $item)
    <div class="col-12 col-md-6 col-lg-4 col-xl-3 d-flex">
        <div class="card flex-fill">
            @if ($item->media_type == 'image')
                <a href="{{ Storage::url($item->file_path) }}" data-fancybox="gallery" data-caption="{{ $item->description ?: $item->file_name }}">
                    <img alt="{{ $item->description ?: $item->file_name }}" src="{{ Storage::url($item->file_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                </a>
            @elseif ($item->media_type == 'video')
                <div class="position-relative" style="height: 200px; background-color: #000;">
                     <video controls width="100%" height="200" style="object-fit: cover;">
                        <source src="{{ Storage::url($item->file_path) }}" type="{{ Storage::mimeType($item->file_path) }}">
                        متصفحك لا يدعم عرض الفيديو.
                    </video>
                     <a href="{{ Storage::url($item->file_path) }}" data-fancybox="gallery" data-type="video" data-caption="{{ $item->description ?: $item->file_name }}" class="position-absolute top-50 start-50 translate-middle text-white fs-1">
                        <i class="fas fa-play-circle opacity-75"></i>
                    </a>
                </div>
            @endif
            <div class="card-body">
                <h5 class="card-title fs-6">{{ Str::limit($item->file_name, 25) }}</h5>
                <p class="card-text text-muted small">
                    {{ Str::limit($item->description, 50) ?: 'لا يوجد وصف' }}<br>
                    نوع: {{ $item->media_type == 'image' ? 'صورة' : 'فيديو' }} | تاريخ الرفع: {{ $item->uploaded_at->format('Y-m-d') }}
                </p>
                <form action="{{ route('patient_management.media.destroy', [$patient->id, $item->id]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                        <i class="fa fa-trash-alt"></i> حذف
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@if(isset($show_pagination) && $show_pagination)
    <div class="mt-3">
        {{ $mediaItems->links() }}
    </div>
@endif
@else
    @if(!isset($hide_empty_message))
    <p>لا توجد وسائط لعرضها.</p>
    @endif
@endif

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
  Fancybox.bind("[data-fancybox]", {
    // Your custom options
  });
</script>
@endpush