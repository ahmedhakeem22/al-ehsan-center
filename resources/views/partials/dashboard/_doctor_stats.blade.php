<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
    <div class="dash-widget">
        <div class="dash-boxs comman-flex-center">
            <img src="{{ URL::asset('/assets/img/icons/dash-users.svg') }}" alt="مرضى">
        </div>
        <div class="dash-content dash-count">
            <h4>المرضى النشطون</h4>
            <h2><span class="counter-up">{{ $data['activePatients'] ?? 0 }}</span></h2>
        </div>
    </div>
</div>
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
    <div class="dash-widget">
        <div class="dash-boxs comman-flex-center">
            <img src="{{ URL::asset('/assets/img/icons/dash-lab.svg') }}" alt="نتائج معملية">
        </div>
        <div class="dash-content dash-count">
            <h4>نتائج معملية معلقة</h4>
            <h2><span class="counter-up">{{ $data['pendingLabResults'] ?? 0 }}</span></h2>
        </div>
    </div>
</div>