<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGymModuleAccessRequest;
use App\Http\Requests\Admin\UpdateGymModuleAccessRequest;
use App\Models\Gym;
use App\Models\GymModuleAccess;
use App\Services\GymModuleAccessService;
use Illuminate\Http\Request;

class AdminGymModuleAccessController extends Controller
{
    public function __construct(
        protected GymModuleAccessService $moduleService
    ) {}

    /**
     * Show module access for a specific gym.
     */
    public function index(Gym $gym)
    {
        $gym->load(['activeSubscription.saasPlan', 'activeModuleAccess.grantedByUser']);
        $modules = $this->moduleService->getEffectiveModules($gym->id);

        return view('admin.gym-module-access.index', compact('gym', 'modules'));
    }

    /**
     * Show form to create a new override.
     */
    public function create(Gym $gym)
    {
        $gym->load('activeSubscription.saasPlan');
        $modules = $this->moduleService->getEffectiveModules($gym->id);

        // Only show modules that don't already have an override
        $availableModules = [];
        foreach ($modules as $code => $data) {
            if (!$data['override']) {
                $availableModules[$code] = $data['label'];
            }
        }

        return view('admin.gym-module-access.create', compact('gym', 'availableModules'));
    }

    /**
     * Store a new override.
     */
    public function store(StoreGymModuleAccessRequest $request)
    {
        $this->moduleService->createOverride($request->validated());

        return redirect()
            ->route('admin.gym-module-access.index', $request->gym_id)
            ->with('success', 'Module access override created.');
    }

    /**
     * Toggle an override on/off.
     */
    public function toggle(GymModuleAccess $module_access)
    {
        $this->moduleService->toggleOverride($module_access);

        return back()->with('success', 'Module access updated.');
    }

    /**
     * Update an override (reason, expiry).
     */
    public function update(UpdateGymModuleAccessRequest $request, GymModuleAccess $module_access)
    {
        $this->moduleService->updateOverride($module_access, $request->validated());

        return back()->with('success', 'Module override updated.');
    }

    /**
     * Delete an override (revert to plan default).
     */
    public function destroy(GymModuleAccess $module_access)
    {
        $gymId = $module_access->gym_id;
        $this->moduleService->deleteOverride($module_access);

        return redirect()
            ->route('admin.gym-module-access.index', $gymId)
            ->with('success', 'Override removed. Module access reverted to plan default.');
    }
}
