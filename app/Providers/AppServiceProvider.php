<?php

namespace App\Providers;

use App\Models\ExerciseTemplate;
use App\Policies\ExerciseTemplatePolicy;
use App\Models\MemberExercisePlan;
use App\Policies\MemberExercisePlanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\DietPlanTemplate;
use App\Policies\DietPlanTemplatePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ExerciseTemplate::class, ExerciseTemplatePolicy::class);
        Gate::policy(MemberExercisePlan::class, MemberExercisePlanPolicy::class);
        Gate::policy(DietPlanTemplate::class, DietPlanTemplatePolicy::class);
    }
}
