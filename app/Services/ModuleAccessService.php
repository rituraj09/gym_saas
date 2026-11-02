<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\GymSubscription;
use App\Models\SaasPlan;

class ModuleAccessService
{
    protected array $moduleMap = [
        'exercise' => 'has_exercise_module',
        'diet' => 'has_diet_module',
        'reports' => 'has_reports_module',
    ];

    protected array $moduleLabels = [
        'exercise' => 'Exercise Management',
        'diet' => 'Diet Plans',
        'reports' => 'Reports & Analytics',
    ];

    public function hasAccess(Gym $gym, string $module): bool
    {
        if (!isset($this->moduleMap[$module])) {
            return false;
        }

        $subscription = $gym->activeSubscription();

        if (!$subscription || !$subscription->saasPlan) {
            return false;
        }

        $column = $this->moduleMap[$module];
        return (bool) $subscription->saasPlan->{$column};
    }

    public function getModuleLabel(string $module): string
    {
        return $this->moduleLabels[$module] ?? ucfirst($module);
    }

    public function getAvailableModules(Gym $gym): array
    {
        $available = [];
        foreach (array_keys($this->moduleMap) as $module) {
            if ($this->hasAccess($gym, $module)) {
                $available[] = $module;
            }
        }
        return $available;
    }

    public function getUnavailableModules(Gym $gym): array
    {
        $unavailable = [];
        foreach (array_keys($this->moduleMap) as $module) {
            if (!$this->hasAccess($gym, $module)) {
                $unavailable[] = [
                    'key' => $module,
                    'label' => $this->getModuleLabel($module),
                ];
            }
        }
        return $unavailable;
    }
}
