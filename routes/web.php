<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\GymRegistrationController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\GymProfileController;
use App\Http\Controllers\Owner\MemberController;
use App\Http\Controllers\Owner\OwnerProfileController;
use App\Http\Controllers\Owner\TrainerController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Owner\AssignmentController;
use App\Http\Controllers\Owner\MembershipPlanController;
use App\Http\Controllers\Owner\MembershipController;
use App\Http\Controllers\Owner\PaymentController;
use App\Http\Controllers\Owner\OwnerExerciseController;
use App\Http\Controllers\Owner\OwnerExerciseTemplateController;
use App\Http\Controllers\Owner\OwnerMemberExercisePlanController;
use App\Http\Controllers\Owner\OwnerDietPlanTemplateController;
use App\Http\Controllers\Owner\OwnerMemberDietPlanController;

use App\Http\Controllers\Owner\OwnerSubscriptionWarningController;
use App\Http\Controllers\Owner\OwnerModuleUpgradeController;

use App\Http\Controllers\Owner\OwnerSubscriptionController;
use App\Http\Controllers\Owner\OwnerReportController;
use App\Http\Controllers\Owner\AuditLogController as OwnerAuditLogController;

use App\Http\Controllers\Admin\AdminSaasPlanController;
use App\Http\Controllers\Admin\AdminGymSubscriptionController;
use App\Http\Controllers\Admin\AdminSaasPaymentController;
use App\Http\Controllers\Admin\AdminGymModuleAccessController;
use App\Http\Controllers\Admin\AdminGymController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;

use App\Http\Controllers\Trainer\DashboardController as TrainerDashboardController;
use App\Http\Controllers\Trainer\MemberController as TrainerMemberController;
use App\Http\Controllers\Trainer\ExercisePlanController as TrainerExercisePlanController;
use App\Http\Controllers\Trainer\DietPlanController as TrainerDietPlanController;
use App\Http\Controllers\Trainer\NotificationController as TrainerNotificationController;
use App\Http\Controllers\Trainer\ProfileController as TrainerProfileController;

use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\ExercisePlanController as MemberExercisePlanController;
use App\Http\Controllers\Member\DietPlanController as MemberDietPlanController;
use App\Http\Controllers\Member\MembershipController as MemberMembershipController;
use App\Http\Controllers\Member\NotificationController as MemberNotificationController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;

use App\Http\Controllers\NotificationController;
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
     Route::get('/gym-subscriptions/show/{subscription}', [AdminGymSubscriptionController::class, 'show'])
            ->name('gym-subscriptions.show');
     Route::get('/gym-subscriptions/edit/{subscription}', [AdminGymSubscriptionController::class, 'edit'])
            ->name('gym-subscriptions.edit');
      Route::put('/gym-subscriptions/edit/{subscription}', [AdminGymSubscriptionController::class, 'update'])
            ->name('gym-subscriptions.update');

        Route::post('/gym-subscriptions/{subscription}/cancel', [AdminGymSubscriptionController::class, 'cancel'])
            ->name('gym-subscriptions.cancel');

        // SaaS Payments
        Route::resource('saas-payments', AdminSaasPaymentController::class)
            ->parameters(['saas-payments' => 'payment'])
            ->only(['index', 'create', 'store', 'show', 'destroy']);


        // Gym Module Access
        Route::get('gyms/{gym}/module-access', [AdminGymModuleAccessController::class, 'index'])
            ->name('gym-module-access.index');
        Route::get('gyms/{gym}/module-access/create', [AdminGymModuleAccessController::class, 'create'])
            ->name('gym-module-access.create');
        Route::post('gyms/{gym}/module-access', [AdminGymModuleAccessController::class, 'store'])
            ->name('gym-module-access.store');
        Route::post('module-access/{module_access}/toggle', [AdminGymModuleAccessController::class, 'toggle'])
            ->name('gym-module-access.toggle');
        Route::put('module-access/{module_access}', [AdminGymModuleAccessController::class, 'update'])
            ->name('gym-module-access.update');
        Route::delete('module-access/{module_access}', [AdminGymModuleAccessController::class, 'destroy'])
            ->name('gym-module-access.destroy');

        // All Gyms
        Route::get('gyms', [AdminGymController::class, 'index'])->name('gyms.index');
        Route::get('gyms/{gym}', [AdminGymController::class, 'show'])->name('gyms.show');

        // All Users
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Reports
        Route::get('reports', [AdminReportController::class, 'overview'])->name('reports.overview');

        // Settings
        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // My Profile
        Route::get('/my-profile', [AdminProfileController::class, 'edit'])->name('my-profile.edit');
        Route::put('/my-profile', [AdminProfileController::class, 'update'])->name('my-profile.update');

        //Audit Logs
        Route::get('audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
    });

    Route::middleware('role:gym_owner')->prefix('owner')->name('owner.')->group(function () {
        // Subscription expired page (accessible even without active subscription)
        Route::get('/subscription-expired', [OwnerSubscriptionController::class, 'expired'])
            ->name('subscription-expired');
        Route::middleware(['subscription.active'])->group(function () {
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
            Route::get('/exercise-templates/{encryptedId}', [OwnerExerciseTemplateController::class, 'show'])
                ->name('exercise-templates.show');
            Route::get('/exercise-templates/{encryptedId}/edit', [OwnerExerciseTemplateController::class, 'edit'])
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
            Route::get('/member-exercise-plans/{encryptedId}', [OwnerMemberExercisePlanController::class, 'show'])
                ->name('member-exercise-plans.show');
            Route::get('/member-exercise-plans/{encryptedId}/edit', [OwnerMemberExercisePlanController::class, 'edit'])
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

                // Diet Plans (feature-gated)
            Route::resource('diet-plan-templates', OwnerDietPlanTemplateController::class)
                ->middleware('subscription.feature:diet');
            Route::resource('member-diet-plans', OwnerMemberDietPlanController::class)
                ->middleware('subscription.feature:diet');

            // Reports (feature-gated)
            Route::prefix('reports')->name('reports.')->middleware('subscription.feature:reports')->group(function () {
                Route::get('/', [OwnerReportController::class, 'overview'])->name('overview');
                Route::get('/revenue', [OwnerReportController::class, 'revenue'])->name('revenue');
                Route::get('/membership', [OwnerReportController::class, 'membership'])->name('membership');
                Route::get('/activity', [OwnerReportController::class, 'activity'])->name('activity');
            });

            //Audit Logs (feature-gated)
            Route::get('audit-logs', [OwnerAuditLogController::class, 'index'])->name('audit-logs.index');
        });

    });


    // Notifications (all authenticated users)
    Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/recent', [NotificationController::class, 'recent'])->name('recent');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });


    // ── Trainer Routes ──
    Route::prefix('trainer')->name('trainer.')->middleware(['auth', 'role:trainer', 'account.active'])->group(function () {
        Route::get('dashboard', [TrainerDashboardController::class, 'index'])->name('dashboard');

        Route::get('members', [TrainerMemberController::class, 'index'])->name('members.index');
        Route::get('members/{member}', [TrainerMemberController::class, 'show'])->name('members.show');

        Route::get('exercise-plans', [TrainerExercisePlanController::class, 'index'])
        ->name('exercise-plans.index');
        Route::get('exercise-plans/create', [TrainerExercisePlanController::class, 'create'])
            ->name('exercise-plans.create');
        Route::post('exercise-plans', [TrainerExercisePlanController::class, 'store'])
            ->name('exercise-plans.store');
        Route::get('exercise-plans/{encryptedId}/edit', [TrainerExercisePlanController::class, 'edit'])
            ->name('exercise-plans.edit');
        Route::patch('exercise-plans/{encryptedId}', [
            TrainerExercisePlanController::class,
            'update'
        ])->name('exercise-plans.update');

        Route::get('/exercise-plans/{encryptedId}', [
            TrainerExercisePlanController::class,
            'show'
        ])->name('exercise-plans.show');
        Route::patch('exercise-plans/{encryptedId}/toggle', [TrainerExercisePlanController::class, 'toggleActive'])
            ->name('exercise-plans.toggle');
        Route::delete('exercise-plans/{encryptedId}', [TrainerExercisePlanController::class, 'destroy'])
            ->name('exercise-plans.destroy');

        Route::get('diet-plans', [TrainerDietPlanController::class, 'index'])->name('diet-plans.index');
        Route::get('diet-plans/create', [TrainerDietPlanController::class, 'create'])->name('diet-plans.create');
        Route::post('diet-plans', [TrainerDietPlanController::class, 'store'])->name('diet-plans.store');
        Route::get('diet-plans/{diet_plan}', [TrainerDietPlanController::class, 'show'])->name('diet-plans.show');
        Route::patch('diet-plans/{diet_plan}/toggle', [TrainerDietPlanController::class, 'toggleActive'])->name('diet-plans.toggle');
        Route::delete('diet-plans/{diet_plan}', [TrainerDietPlanController::class, 'destroy'])->name('diet-plans.destroy');

        Route::get('notifications', [TrainerNotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/{notification}/read', [TrainerNotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('notifications/mark-all-read', [TrainerNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

        Route::get('profile', [TrainerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile/password', [TrainerProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // ── Member Routes ──
    Route::prefix('member')->name('member.')->middleware(['auth', 'role:member', 'account.active'])->group(function () {
        Route::get('dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');

        Route::get('exercise-plan', [MemberExercisePlanController::class, 'index'])->name('exercise-plan.index');
        Route::get('diet-plan', [MemberDietPlanController::class, 'index'])->name('diet-plan.index');
        Route::get('membership', [MemberMembershipController::class, 'index'])->name('membership.index');

        Route::get('notifications', [MemberNotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/{notification}/read', [MemberNotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('notifications/mark-all-read', [MemberNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

        Route::get('profile', [MemberProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile/password', [MemberProfileController::class, 'updatePassword'])->name('profile.password');
    });

});

require __DIR__ . '/auth.php';
