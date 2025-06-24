{{-- مثال لإحصائية واحدة --}}
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
    <div class="dash-widget">
        <div class="dash-boxs comman-flex-center">
            <img src="{{ URL::asset('/assets/img/icons/dash-users.svg') }}" alt="مرضى">
        </div>
        <div class="dash-content dash-count">
            <h4>إجمالي المرضى النشطين</h4>
            <h2><span class="counter-up">{{ $data['totalPatients'] ?? 0 }}</span></h2>
            <p><span class="text-success"><i class="fa fa-arrow-up"></i> --.--%</span> مقارنة بالشهر الماضي</p>
        </div>
    </div>
</div>
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
    <div class="dash-widget">
        <div class="dash-boxs comman-flex-center">
            <img src="{{ URL::asset('/assets/img/icons/dash-bed.svg') }}" alt="أسرة">
        </div>
        <div class="dash-content dash-count">
            <h4>الأسرة المشغولة</h4>
            <h2><span class="counter-up">{{ $data['occupiedBeds'] ?? 0 }}</span></h2>
            <p>من إجمالي {{ $data['totalBeds'] ?? 0 }} ({{ $data['occupancyPercentage'] ?? 0 }}%)</p>
        </div>
    </div>
</div>
 <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
    <div class="dash-widget">
        <div class="dash-boxs comman-flex-center">
            <img src="{{ URL::asset('/assets/img/icons/dash-bed-empty.svg') }}" alt="أسرة شاغرة">
        </div>
        <div class="dash-content dash-count">
            <h4>الأسرة الشاغرة</h4>
            <h2><span class="counter-up">{{ $data['vacantBeds'] ?? 0 }}</span></h2>
             <p> </p> {{-- للحفاظ على التنسيق --}}
        </div>
    </div>
</div>
 <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
    <div class="dash-widget">
        <div class="dash-boxs comman-flex-center">
            <img src="{{ URL::asset('/assets/img/icons/dash-team.svg') }}" alt="مستخدمون">
        </div>
        <div class="dash-content dash-count">
            <h4>إجمالي المستخدمين</h4>
            <h2><span class="counter-up">{{ $data['totalUsers'] ?? 0 }}</span></h2>
             <p> </p>
        </div>
    </div>
</div>