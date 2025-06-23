<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('activity_type')) {
            $query->where('activity_type', 'like', '%' . $request->activity_type . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('log_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('log_time', '<=', $request->date_to);
        }

        $activityLogs = $query->paginate(20)->withQueryString();
        $usersForFilter =User::orderBy('name')->pluck('name', 'id');
        return view('admin.activity_logs.index', compact('activityLogs', 'usersForFilter'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('admin.activity_logs.show', compact('activityLog'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();
        return redirect()->route('admin.activity_logs.index')->with('success', 'Activity log entry deleted successfully.');
    }

    public function clearOldLogs(Request $request)
    {
        $request->validate(['days' => 'required|integer|min:1']);
        $cutoffDate = now()->subDays($request->days);
        $deletedCount = ActivityLog::where('log_time', '<', $cutoffDate)->delete();
        return redirect()->route('admin.activity_logs.index')->with('success', "$deletedCount old activity log entries deleted successfully.");
    }
}