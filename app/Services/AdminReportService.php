<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\GymMemberPayment;
use App\Models\GymSubscription;
use App\Models\Member;
use App\Models\SaasPlan;
use App\Models\Trainer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportService
{
    public function getOverviewSummary(): array
    {
        $now = now();

        return [
            'total_gyms' => Gym::count(),
            'active_gyms' => Gym::whereHas('activeSubscription', fn($q) =>
                $q->whereHas('status', fn($s) => $s->where('code', 'active'))
            )->count(),
            'total_users' => User::count(),
            'total_members' => Member::count(),
            'total_trainers' => Trainer::count(),
            'total_revenue' => GymMemberPayment::whereHas('status', fn($q) => $q->where('code', 'completed'))->sum('amount'),
            'revenue_this_month' => GymMemberPayment::whereHas('status', fn($q) => $q->where('code', 'completed'))
                ->whereMonth('payment_date', $now->month)
                ->whereYear('payment_date', $now->year)
                ->sum('amount'),
            'active_subscriptions' => GymSubscription::whereHas('status', fn($q) => $q->where('code', 'active'))->count(),
            'expiring_subscriptions' => GymSubscription::whereHas('status', fn($q) => $q->whereIn('code', ['active', 'trial']))
                ->whereBetween('end_date', [$now, $now->addDays(7)])
                ->count(),
        ];
    }

    public function getRevenueTrend(int $months = 12): array
    {
        $labels = [];
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $total = GymMemberPayment::whereHas('status', fn($q) => $q->where('code', 'completed'))
                ->whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');

            $data[] = round($total, 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getSubscriptionBreakdown(): array
    {
        $byPlan = GymSubscription::select('saas_plans.name', DB::raw('count(*) as count'))
            ->join('saas_plans', 'gym_subscriptions.saas_plan_id', '=', 'saas_plans.id')
            ->whereHas('status', fn($q) => $q->whereIn('code', ['active', 'trial']))
            ->groupBy('saas_plans.name')
            ->pluck('count', 'name')
            ->toArray();

        return [
            'labels' => array_keys($byPlan),
            'data' => array_values($byPlan),
        ];
    }

    public function getGymGrowth(int $months = 12): array
    {
        $labels = [];
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $count = Gym::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $data[] = $count;
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
