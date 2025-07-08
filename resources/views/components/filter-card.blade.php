{{--
    This is a reusable filter card component.
    It expects the following variables to be passed:
    - $route: The URL where the filter form should be submitted.
    - $filters: An associative array describing the filter fields.
        Example:
        'filter_name' => [
            'label' => 'Filter Label',
            'type' => 'text' | 'select' | 'date',
            'options' => (optional) an array of [value => text] for select fields
        ]
--}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-filter me-2"></i> فلترة النتائج
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ $route }}" method="GET">
            <div class="row">
                @foreach($filters as $name => $details)
                    <div class="col-md-4 col-sm-6 mb-3">
                        <label for="filter_{{ $name }}" class="form-label">{{ $details['label'] }}</label>

                        @if($details['type'] == 'select')
                            <select name="{{ $name }}" id="filter_{{ $name }}" class="form-select">
                                <option value="">الكل</option>
                                @foreach($details['options'] as $value => $text)
                                    <option value="{{ $value }}" @selected(request($name) == $value)>
                                        {{ $text }}
                                    </option>
                                @endforeach
                            </select>

                        @elseif($details['type'] == 'date')
                            <input type="date" name="{{ $name }}" id="filter_{{ $name }}" class="form-control" value="{{ request($name) }}">

                        @else {{-- Default to text input --}}
                            <input type="text" name="{{ $name }}" id="filter_{{ $name }}" class="form-control" value="{{ request($name) }}" placeholder="{{ $details['label'] }}">
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="row">
                 <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                    <a href="{{ $route }}" class="btn btn-secondary">
                        <i class="fas fa-eraser me-1"></i> مسح الفلتر
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>