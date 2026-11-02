<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('platform_admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('gym_owner')) {
            return redirect()->route('owner.dashboard');
        }
        if ($user->hasRole('trainer')) {
            return redirect()->route('trainer.dashboard');
        }
        if ($user->hasRole('member')) {
            return redirect()->route('member.dashboard');
        }

        return redirect('/');
    }
}
