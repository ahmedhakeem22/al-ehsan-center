@props(['recentActivities' => null])

<div class="notification-box">
    <div class="msg-sidebar notifications msg-noti">
        <div class="topnav-dropdown-header">
            <span>سجل الأنشطة الأخيرة</span>
        </div>
        <div class="drop-scroll msg-list-scroll" id="msg_list">
            @if ($recentActivities && $recentActivities->count() > 0)
                <ul class="list-box">
                    @foreach ($recentActivities as $activity)
                        <li>
                            <a>
                                <div class="list-item">
                                    <div class="list-left">
                                        <span class="avatar">
                                            {{ $activity->user ? strtoupper(substr(explode(' ', $activity->user->name)[0], 0, 1)) : 'N' }}
                                        </span>
                                    </div>
                                    <div class="list-body">
                                        <span class="message-author">
                                            {{ $activity->user->name ?? 'النظام' }}
                                            <small class="text-muted"> ({{ Str::limit($activity->activity_type, 20) }})</small>
                                        </span>
                                        <span class="message-time">{{ $activity->log_time ? \Carbon\Carbon::parse($activity->log_time)->locale('ar')->diffForHumans() : '' }}</span>
                                        <div class="clearfix"></div>
                                        <span class="message-content">{{ Str::limit($activity->description, 60) }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-center p-3">لا توجد أنشطة حديثة.</p>
            @endif
        </div>
        <div class="topnav-dropdown-footer">
            <a href="{{-- {{ route('activity-logs.index') }} --}}">عرض كل الأنشطة</a>
        </div>
    </div>
</div>