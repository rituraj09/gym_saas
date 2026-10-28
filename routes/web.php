<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\GymRegistrationController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\GymProfileController;
use App\Http\Controllers\Owner\MemberController;
use App\Http\Controllers\Owner\OwnerProfileController;
use App\Http\Controllers\Owner\TrainerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Trainer\DashboardController as TrainerDashboardController;
use App\Http\Controllers\Owner\AssignmentController;
use App\Http\Controllers\Owner\MembershipPlanController;
use App\Http\Controllers\Owner\MembershipController;
use App\Http\Controllers\Owner\PaymentController;
use App\Http\Controllers\Owner\OwnerExerciseController;
use App\Http\Controllers\Owner\OwnerExerciseTemplateController;
use App\Http\Controllers\Owner\OwnerMemberExercisePlanController;
use App\Http\Controllers\Owner\OwnerDietPlanTemplateController;
use App\Http\Controllers\Owner\OwnerMemberDietPlanController;
use App\Http\Controllers\Admin\AdminSaasPlanController;
use App\Http\Controllers\Admin\AdminGymSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/register-gym', [GymRegistrationController::class, 'create'])->name('gym.register');
    Route::post('/register-gym', [GymRegistrationController::class, 'store']);
});

Route::get('/dashboard', DashboardRedirectController::class)
    ->middleware(['auth', 'verified', 'account.active'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'account.active'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:platform_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // SaaS Plans
        Route::get('/saas-plans', [AdminSaasPlanController::class, 'index'])
            ->name('saas-plans.index');
        Route::get('/saas-plans/create', [AdminSaasPlanController::class, 'create'])
            ->name('saas-plans.create');
        Route::post('/saas-plans', [AdminSaasPlanController::class, 'store'])
            ->name('saas-plans.store');
        Route::get('/saas-plans/{plan}', [AdminSaasPlanController::class, 'show'])
            ->name('saas-plans.show');
        Route::get('/saas-plans/{plan}/edit', [AdminSaasPlanController::class, 'edit'])
            ->name('saas-plans.edit');
        Route::put('/saas-plans/{plan}', [AdminSaasPlanController::class, 'update'])
            ->name('saas-plans.update');
        Route::post('/saas-plans/{plan}/toggle', [AdminSaasPlanController::class, 'toggleStatus'])
            ->name('saas-plans.toggle');
        Route::delete('/saas-plans/{plan}', [AdminSaasPlanController::class, 'destroy'])
            ->name('saas-plans.destroy');

        // Gym Subscriptions
        Route::get('/gym-subscriptions', [AdminGymSubscriptionController::class, 'index'])
            ->name('gym-subscriptions.index');
        Route::get('/gym-subscriptions/create', [AdminGymSubscriptionController::class, 'create'])
            ->name('gym-subscriptions.create');
        Route::post('/gym-subscriptions', [AdminGymSubscriptionController::class, 'store'])
            ->name('gym-subscriptions.store');
        Route::post('/gym-subscriptions/{subscription}/cancel', [AdminGymSubscriptionController::class, 'cancel'])
            ->name('gym-subscriptions.cancel');
    });

    Route::middleware('role:gym_owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

        Route::get('/gym-profile', [GymProfileController::class, 'edit'])->name('gym-profile.edit');
        Route::put('/gym-profile', [GymProfileController::class, 'update'])->name('gym-profile.update');

        Route::get('/my-profile', [OwnerProfileController::class, 'edit'])->name('my-profile.edit');
        Route::put('/my-profile', [OwnerProfileController::class, 'update'])->name('my-profile.update');

        // Trainers
        Route::get('/trainers', [TrainerController::class, 'index'])->name('trainers.index');
        Route::get('/trainers/create', [TrainerController::class, 'create'])->name('trainers.create');
        Route::post('/trainers', [TrainerController::class, 'store'])->name('trainers.store');
        Route::get('/trainers/{trainer}', [TrainerController::class, 'show'])->name('trainers.show');
        Route::get('/trainers/{trainer}/edit', [TrainerController::class, 'edit'])->name('trainers.edit');
        Route::put('/trainers/{trainer}', [TrainerController::class, 'update'])->name('trainers.update');
        Route::post('/trainers/{trainer}/toggle-status', [TrainerController::class, 'toggleStatus'])->name('trainers.toggle-status');

        // Members
        Route::get('/members', [MemberController::class, 'index'])->name('members.index');
        Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::post('/members/{member}/toggle-status', [MemberController::class, 'toggleStatus'])->name('members.toggle-status');

        // Assignments
        Route::get('/members/{member}/assign-trainer', [AssignmentController::class, 'createForMember'])->name('assignments.create');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::post('/members/{member}/unassign-trainer', [AssignmentController::class, 'unassign'])->name('assignments.unassign');

        // Membership Plans
        Route::resource('membership-plans', MembershipPlanController::class)->except(['destroy']);
        Route::post('/membership-plans/{membershipPlan}/toggle-status', [MembershipPlanController::class, 'toggleStatus'])->name('membership-plans.toggle-status');

        // Memberships
        Route::get('/memberships', [MembershipController::class, 'index'])->name('memberships.index');
        Route::get('/memberships/create', [MembershipController::class, 'create'])->name('memberships.create');
        Route::post('/memberships', [MembershipController::class, 'store'])->name('memberships.store');
        Route::get('/memberships/{membership}', [MembershipController::class, 'show'])->name('memberships.show');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

        // Exercise Library (read-only browse)
        Route::get('/exercises', [OwnerExerciseController::class, 'index'])
            ->name('exercises.index');

        // Exercise Templates (CRUD)
        Route::get('/exercise-templates', [OwnerExerciseTemplateController::class, 'index'])
            ->name('exercise-templates.index');
        Route::get('/exercise-templates/create', [OwnerExerciseTemplateController::class, 'create'])
            ->name('exercise-templates.create');
        Route::post('/exercise-templates', [OwnerExerciseTemplateController::class, 'store'])
            ->name('exercise-templates.store');
        Route::get('/exercise-templates/{template}', [OwnerExerciseTemplateController::class, 'show'])
            ->name('exercise-templates.show');
        Route::get('/exercise-templates/{template}/edit', [OwnerExerciseTemplateController::class, 'edit'])
            ->name('exercise-templates.edit');
        Route::put('/exercise-templates/{template}', [OwnerExerciseTemplateController::class, 'update'])
            ->name('exercise-templates.update');
        Route::post('/exercise-templates/{template}/toggle', [OwnerExerciseTemplateController::class, 'toggleStatus'])
            ->name('exercise-templates.toggle');
        Route::delete('/exercise-templates/{template}', [OwnerExerciseTemplateController::class, 'destroy'])
            ->name('exercise-templates.destroy');

        // Member Exercise Plans
        Route::get('/member-exercise-plans', [OwnerMemberExercisePlanController::class, 'index'])
            ->name('member-exercise-plans.index');
        Route::get('/member-exercise-plans/create', [OwnerMemberExercisePlanController::class, 'create'])
            ->name('member-exercise-plans.create');
        Route::post('/member-exercise-plans', [OwnerMemberExercisePlanController::class, 'store'])
            ->name('member-exercise-plans.store');
        Route::get('/member-exercise-plans/{plan}', [OwnerMemberExercisePlanController::class, 'show'])
            ->name('member-exercise-plans.show');
        Route::get('/member-exercise-plans/{plan}/edit', [OwnerMemberExercisePlanController::class, 'edit'])
            ->name('member-exercise-plans.edit');
        Route::put('/member-exercise-plans/{plan}', [OwnerMemberExercisePlanController::class, 'update'])
            ->name('member-exercise-plans.update');
        Route::post('/member-exercise-plans/{plan}/toggle', [OwnerMemberExercisePlanController::class, 'toggleStatus'])
            ->name('member-exercise-plans.toggle');
        Route::delete('/member-exercise-plans/{plan}', [OwnerMemberExercisePlanController::class, 'destroy'])
            ->name('member-exercise-plans.destroy');
        Route::post('/member-exercise-plans/{plan}/log', [OwnerMemberExercisePlanController::class, 'logWorkout'])
            ->name('member-exercise-plans.log');

        // Diet Plan Templates
        Route::get('/diet-plan-templates', [OwnerDietPlanTemplateController::class, 'index'])
            ->name('diet-plan-templates.index');
        Route::get('/diet-plan-templates/create', [OwnerDietPlanTemplateController::class, 'create'])
            ->name('diet-plan-templates.create');
        Route::post('/diet-plan-templates', [OwnerDietPlanTemplateController::class, 'store'])
            ->name('diet-plan-templates.store');
        Route::get('/diet-plan-templates/{template}', [OwnerDietPlanTemplateController::class, 'show'])
            ->name('diet-plan-templates.show');
        Route::get('/diet-plan-templates/{template}/edit', [OwnerDietPlanTemplateController::class, 'edit'])
            ->name('diet-plan-templates.edit');
        Route::put('/diet-plan-templates/{template}', [OwnerDietPlanTemplateController::class, 'update'])
            ->name('diet-plan-templates.update');
        Route::post('/diet-plan-templates/{template}/toggle', [OwnerDietPlanTemplateController::class, 'toggleStatus'])
            ->name('diet-plan-templates.toggle');
        Route::delete('/diet-plan-templates/{template}', [OwnerDietPlanTemplateController::class, 'destroy'])
            ->name('diet-plan-templates.destroy');

        // Member Diet Plans
        Route::get('/member-diet-plans', [OwnerMemberDietPlanController::class, 'index'])
            ->name('member-diet-plans.index');
        Route::get('/member-diet-plans/create', [OwnerMemberDietPlanController::class, 'create'])
            ->name('member-diet-plans.create');
        Route::post('/member-diet-plans', [OwnerMemberDietPlanController::class, 'store'])
            ->name('member-diet-plans.store');
        Route::get('/member-diet-plans/{plan}', [OwnerMemberDietPlanController::class, 'show'])
            ->name('member-diet-plans.show');
        Route::post('/member-diet-plans/{plan}/toggle', [OwnerMemberDietPlanController::class, 'toggleStatus'])
            ->name('member-diet-plans.toggle');
        Route::delete('/member-diet-plans/{plan}', [OwnerMemberDietPlanController::class, 'destroy'])
            ->name('member-diet-plans.destroy');
        Route::post('/member-diet-plans/{plan}/log', [OwnerMemberDietPlanController::class, 'logDiet'])
            ->name('member-diet-plans.log');
    });

    Route::middleware('role:trainer')->prefix('trainer')->name('trainer.')->group(function () {
        Route::get('/dashboard', [TrainerDashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('role:member')->prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    });
});

require __DIR__ . '/auth.php';
