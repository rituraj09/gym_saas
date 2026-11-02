<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * Owner: View audit logs for their gym only.
     */
    public function index(Request $request)
    {
        $gymId = Auth::user()->gymOwner?->gym_id;

        if (! $gymId) {
            abort(403, 'Gym not found.');
        }

        $query = AuditLog::with(['user'])
            ->forGym($gymId)
            ->latest('created_at');

        // Apply filters
        $query->byAction($request->action)
              ->byModel($request->model_type)
              ->byUser($request->user_id)
              ->byDateRange($request->date_from, $request->date_to);

        $logs = $query->paginate(25)->withQueryString();

        // Filter options (gym-scoped)
        $users = User::whereHas('trainer', fn ($q) => $q->where('gym_id', $gymId))
            ->orWhereHas('member', fn ($q) => $q->where('gym_id', $gymId))
            ->orWhereHas('gymOwner', fn ($q) => $q->where('gym_id', $gymId))
            ->orderBy('name')
            ->get(['id', 'name']);

        $actions = AuditLog::forGym($gymId)->distinct()->pluck('action')->sort()->values();
        $modelTypes = AuditLog::forGym($gymId)
            ->distinct()
            ->pluck('model_type')
            ->map(fn ($type) => class_basename($type))
            ->sort()
            ->unique()
            ->values();

        return view('owner.audit-logs.index', compact(
            'logs', 'users', 'actions', 'modelTypes'
        ));
    }
}
