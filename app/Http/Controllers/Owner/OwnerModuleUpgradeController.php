<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\ModuleAccessService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerModuleUpgradeController extends Controller
{
    public function show(Request $request, ModuleAccessService $service): View
    {
        $gym = auth()->user()->gymOwner->gym;
        $module = $request->query('module', '');
        $moduleLabel = $service->getModuleLabel($module);
        $currentPlan = $subscription?->saasPlan;
        $unavailableModules = $service->getUnavailableModules($gym);

        return view('owner.module.upgrade', compact(
            'gym',
            'module',
            'moduleLabel',
            'subscription',
            'currentPlan',
            'unavailableModules'
        ));
    }
}
