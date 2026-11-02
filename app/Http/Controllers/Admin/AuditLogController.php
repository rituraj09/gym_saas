<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Admin: View all audit logs with filters.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'gym'])
            ->latest('created_at');

        // Apply filters
        $query->byAction($request->action)
              ->byModel($request->model_type)
              ->byUser($request->user_id)
              ->forGym($request->gym_id)
              ->byDateRange($request->date_from, $request->date_to);

        $logs = $query->paginate(25)->withQueryString();

        // Filter options
        $gyms = Gym::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();
        $modelTypes = AuditLog::distinct()
            ->pluck('model_type')
            ->map(fn ($type) => class_basename($type))
            ->sort()
            ->unique()
            ->values();

        return view('admin.audit-logs.index', compact(
            'logs', 'gyms', 'users', 'actions', 'modelTypes'
        ));
    }
}
