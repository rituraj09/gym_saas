<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountStatus;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'accountStatus', 'gymOwner.gym', 'trainer.gym', 'member.gym']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('code', $request->role));
        }

        if ($request->filled('status')) {
            $query->whereHas('accountStatus', fn($q) => $q->where('code', $request->status));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $roles = UserRole::orderBy('name')->get();
        $statuses = AccountStatus::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'statuses'));
    }

    public function show(User $user)
    {
        $user->load([
            'roles',
            'accountStatus',
            'gymOwner.gym',
            'trainer.gym',
            'member.gym',
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user)
    {
        // Prevent logged-in user from changing their own status
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot deactivate or change your own account status.');
        }

        $activeStatus = AccountStatus::where('code', 'active')->first();
        $inactiveStatus = AccountStatus::where('code', 'inactive')->first();

        if ($user->isActive()) {
            $user->account_status_id = $inactiveStatus?->id;
            $message = "User {$user->name} has been deactivated.";
        } else {
            $user->account_status_id = $activeStatus?->id;
            $message = "User {$user->name} has been activated.";
        }

        $user->save();

        return back()->with('success', $message);
    }
}
