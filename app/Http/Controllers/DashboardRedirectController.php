<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        return match ($user->getPrimaryRole()) {
            'platform_admin' => redirect()->route('admin.dashboard'),
            'gym_owner'      => redirect()->route('owner.dashboard'),
            'trainer'        => redirect()->route('trainer.dashboard'),
            'member'         => redirect()->route('member.dashboard'),
            default          => redirect('/'),
        };
    }
}
