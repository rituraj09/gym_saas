<?php

namespace App\Http\Middleware;

use App\Services\ModuleAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAccess
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (!$user || !$user->gymOwner) {
            return $next($request);
        }

        $gym = $user->gymOwner->gym;
        $service = app(ModuleAccessService::class);

        if (!$service->hasAccess($gym, $module)) {
            return redirect()->route('owner.module.upgrade', ['module' => $module]);
        }

        return $next($request);
    }
}
