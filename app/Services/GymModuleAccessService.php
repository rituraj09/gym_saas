<?php

namespace App\Services;

use App\Models\GymModuleAccess;

class GymModuleAccessService
{
    public function createOverride(array $data): GymModuleAccess
    {
        $data['granted_by'] = auth()->id();

        return GymModuleAccess::create($data);
    }

    public function updateOverride(GymModuleAccess $access, array $data): GymModuleAccess
    {
        $access->update($data);

        return $access->fresh();
    }

    public function deleteOverride(GymModuleAccess $access): void
    {
        $access->delete();
    }

    public function toggleOverride(GymModuleAccess $access): GymModuleAccess
    {
        $access->update(['is_enabled' => !$access->is_enabled]);

        return $access->fresh();
    }

    /**
     * Get the effective module status for a gym.
     * Returns array of module_code => ['enabled' => bool, 'source' => string, 'override' => ?GymModuleAccess]
     */
    public function getEffectiveModules(int $gymId): array
    {
        $gym = \App\Models\Gym::with(['activeSubscription.saasPlan', 'activeModuleAccess'])->find($gymId);

        if (!$gym) {
            return [];
        }

        $plan = $gym->activeSubscription?->saasPlan;
        $overrides = $gym->activeModuleAccess->keyBy('module_code');
        $results = [];

        foreach (GymModuleAccess::MODULES as $code => $label) {
            $override = $overrides->get($code);
            $planDefault = $plan ? $plan->hasFeature($code) : false;

            if ($override) {
                $results[$code] = [
                    'label' => $label,
                    'enabled' => $override->is_enabled,
                    'source' => 'override',
                    'override' => $override,
                    'plan_default' => $planDefault,
                ];
            } else {
                $results[$code] = [
                    'label' => $label,
                    'enabled' => $planDefault,
                    'source' => 'plan',
                    'override' => null,
                    'plan_default' => $planDefault,
                ];
            }
        }

        return $results;
    }
}
