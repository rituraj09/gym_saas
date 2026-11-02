<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\GymStatus;
use Illuminate\Http\Request;

class AdminGymController extends Controller
{
    public function index(Request $request)
    {
        $query = Gym::with(['status', 'activeSubscription.saasPlan', 'activeSubscription.status', 'owners.user'])
            ->withCount(['members', 'trainers']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('code', $request->status));
        }

        if ($request->filled('subscription')) {
            if ($request->subscription === 'none') {
                $query->whereDoesntHave('activeSubscription');
            } else {
                $query->whereHas('activeSubscription', fn($q) =>
                    $q->whereHas('status', fn($s) => $s->where('code', $request->subscription))
                );
            }
        }

        $gyms = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $statuses = GymStatus::orderBy('name')->get();

        return view('admin.gyms.index', compact('gyms', 'statuses'));
    }

    public function show(Gym $gym)
    {
        $gym->load([
            'status',
            'owners.user',
            'activeSubscription.saasPlan',
            'activeSubscription.status',
            'activeSubscription.payments.paymentStatus',
        ])->loadCount(['members', 'trainers', 'membershipPlans']);

        return view('admin.gyms.show', compact('gym'));
    }
}
