<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * GET /api/activity-logs
     */
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user:id,name,email')
            ->when($request->user_id,    fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->model_type, fn($q) => $q->where('model_type', 'like', '%' . $request->model_type . '%'))
            ->when($request->action,     fn($q) => $q->where('action', $request->action))
            ->when($request->date_from,  fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,    fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('model_label', 'like', '%' . $request->search . '%')
                       ->orWhereHas('user', fn($q3) => $q3->where('name', 'like', '%' . $request->search . '%'));
                });
            })
            ->latest()
            ->paginate($request->get('per_page', 20));

        // Append accessor ke setiap item
        $logs->getCollection()->each(function ($log) {
            $log->append(['model_name', 'action_color']);
        });

        return response()->json($logs);
    }

    /**
     * GET /api/activity-logs/{id}
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user:id,name,email');
        $activityLog->append(['model_name', 'action_color']);

        return response()->json($activityLog);
    }

    /**
     * GET /api/activity-logs/stats
     */
    public function stats()
    {
        $today = now()->toDateString();
        $week  = now()->startOfWeek()->toDateString();

        return response()->json([
            'today' => ActivityLog::whereDate('created_at', $today)
                ->selectRaw('action, COUNT(*) as total')
                ->groupBy('action')
                ->pluck('total', 'action'),
            'week'  => ActivityLog::whereDate('created_at', '>=', $week)
                ->selectRaw('action, COUNT(*) as total')
                ->groupBy('action')
                ->pluck('total', 'action'),
            'total' => ActivityLog::count(),
        ]);
    }
}
