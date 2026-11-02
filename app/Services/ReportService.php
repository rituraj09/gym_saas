<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\GymMemberPayment;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MemberDietLog;
use App\Models\MemberWorkoutLog;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    protected int $gymId;

    public function __construct(Gym $gym)
    {
        $this->gymId = $gym->id;
    }

    // ─── OVERVIEW ────────────────────────────────────────

    public function getOverviewSummary(): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        return [
            'total_members' => Member::where('gym_id', $this->gymId)->count(),
            'total_trainers' => Trainer::where('gym_id', $this->gymId)->count(),
            'active_memberships' => Membership::where('gym_id', $this->gymId)
                ->whereHas('status', fn($q) => $q->where('code', 'active'))
                ->count(),
            'revenue_this_month' => GymMemberPayment::where('gym_id', $this->gymId)
                ->where('payment_date', '>=', $startOfMonth)
                ->whereHas('status', fn($q) => $q->where('code', 'completed'))
                ->sum('amount'),
            'revenue_last_month' => GymMemberPayment::where('gym_id', $this->gymId)
                ->whereBetween('payment_date', [
                    $now->copy()->subMonth()->startOfMonth(),
                    $now->copy()->subMonth()->endOfMonth(),
                ])
                ->whereHas('status', fn($q) => $q->where('code', 'completed'))
                ->sum('amount'),
            'new_members_this_month' => Member::where('gym_id', $this->gymId)
                ->where('created_at', '>=', $startOfMonth)
                ->count(),
            'workouts_this_week' => MemberWorkoutLog::whereHas('plan', function ($query) {
                $query->where('gym_id', $this->gymId);
            })
            ->where('workout_date', '>=', $now->copy()->startOfWeek())
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

            $total = GymMemberPayment::where('gym_id', $this->gymId)
                ->whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->whereHas('status', fn($q) => $q->where('code', 'completed'))
                ->sum('amount');

            $data[] = round($total, 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getMembershipBreakdown(): array
    {
        $statuses = Membership::where('gym_id', $this->gymId)
            ->select('membership_statuses.code', DB::raw('count(*) as count'))
            ->join('membership_statuses', 'memberships.membership_status_id', '=', 'membership_statuses.id')
            ->groupBy('membership_statuses.code')
            ->pluck('count', 'code')
            ->toArray();

        return [
            'labels' => array_map('ucfirst', array_keys($statuses)),
            'data' => array_values($statuses),
            'colors' => $this->getStatusColors(array_keys($statuses)),
        ];
    }

    // ─── REVENUE REPORT ──────────────────────────────────


public function getRevenueReport(
    ?string $from = null,
    ?string $to = null,
    ?int $methodId = null
): array {
    $from = $from
        ? Carbon::parse($from)->startOfDay()
        : now()->subYear()->startOfMonth();

    $to = $to
        ? Carbon::parse($to)->endOfDay()
        : now()->endOfMonth();

    $baseQuery = GymMemberPayment::where('gym_id', $this->gymId)
        ->whereBetween('payment_date', [$from, $to])
        ->whereHas('status', fn ($q) => $q->where('code', 'completed'));

    if ($methodId) {
        $baseQuery->where('payment_method_id', $methodId);
    }

    // Monthly breakdown
    $monthly = (clone $baseQuery)
        ->selectRaw('YEAR(payment_date) as year')
        ->selectRaw('MONTH(payment_date) as month')
        ->selectRaw('SUM(amount) as total')
        ->selectRaw('COUNT(*) as count')
        ->groupByRaw('YEAR(payment_date), MONTH(payment_date)')
        ->orderByRaw('YEAR(payment_date) ASC, MONTH(payment_date) ASC')
        ->get();

    $labels = [];
    $data = [];

    foreach ($monthly as $row) {
        $labels[] = Carbon::createFromDate(
            (int) $row->year,
            (int) $row->month,
            1
        )->format('M Y');

        $data[] = round((float) $row->total, 2);
    }

    // Breakdown by payment method
    $byMethod = (clone $baseQuery)
        ->join(
            'payment_methods',
            'gym_member_payments.payment_method_id',
            '=',
            'payment_methods.id'
        )
        ->select('payment_methods.name')
        ->selectRaw('SUM(gym_member_payments.amount) as total')
        ->groupBy('payment_methods.name')
        ->orderByDesc('total')
        ->get();

    // Overall totals use a fresh, ungrouped query
    $total = (clone $baseQuery)->sum('amount');
    $count = (clone $baseQuery)->count();

    return [
        'chart' => [
            'labels' => $labels,
            'data' => $data,
        ],
        'by_method' => $byMethod,
        'total' => round((float) $total, 2),
        'count' => $count,
        'from' => $from->format('Y-m-d'),
        'to' => $to->format('Y-m-d'),
    ];
}


    // ─── MEMBERSHIP REPORT ───────────────────────────────

    public function getMembershipReport(?string $from = null, ?string $to = null): array
    {
        $from = $from ? Carbon::parse($from) : now()->subYear()->startOfMonth();
        $to = $to ? Carbon::parse($to) : now()->endOfMonth();

        // New memberships per month
        $newPerMonth = Membership::where('gym_id', $this->gymId)
            ->whereBetween('start_date', [$from, $to])
            ->select(
                DB::raw('YEAR(start_date) as year'),
                DB::raw('MONTH(start_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $data = [];
        foreach ($newPerMonth as $row) {
            $labels[] = Carbon::createFromDate($row->year, $row->month)->format('M Y');
            $data[] = $row->count;
        }

        // Current status breakdown
        $statusBreakdown = Membership::where('gym_id', $this->gymId)
            ->select('membership_statuses.code', 'membership_statuses.name', DB::raw('count(*) as count'))
            ->join('membership_statuses', 'memberships.membership_status_id', '=', 'membership_statuses.id')
            ->groupBy('membership_statuses.code', 'membership_statuses.name')
            ->orderByDesc('count')
            ->get();

        // Expiring soon (next 7 days)
        $expiringSoon = Membership::where('gym_id', $this->gymId)
            ->whereHas('status', fn($q) => $q->where('code', 'active'))
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->count();

        // Expired this month
        $expiredThisMonth = Membership::where('gym_id', $this->gymId)
            ->whereHas('status', fn($q) => $q->where('code', 'expired'))
            ->whereMonth('end_date', now()->month)
            ->whereYear('end_date', now()->year)
            ->count();

        return [
            'new_trend' => ['labels' => $labels, 'data' => $data],
            'status_breakdown' => $statusBreakdown,
            'expiring_soon' => $expiringSoon,
            'expired_this_month' => $expiredThisMonth,
            'total_active' => Membership::where('gym_id', $this->gymId)
                ->whereHas('status', fn($q) => $q->where('code', 'active'))
                ->count(),
        ];
    }

    // ─── ACTIVITY REPORT ─────────────────────────────────

    public function getActivityReport(?string $from = null, ?string $to = null): array
    {
        $from = $from ? Carbon::parse($from) : now()->subDays(30);
        $to = $to ? Carbon::parse($to) : now();

        // Workout logs per day (last 30 days)
        $dailyWorkouts = MemberWorkoutLog::whereHas('plan', function ($query) {
            $query->where('gym_id', $this->gymId);
            })
            ->whereBetween('workout_date', [$from, $to])
            ->select('workout_date', DB::raw('COUNT(*) as count'))
            ->groupBy('workout_date')
            ->orderBy('workout_date')
            ->pluck('count', 'workout_date')
            ->toArray();

        // Fill in missing dates with 0
        $labels = [];
        $data = [];
        $current = Carbon::parse($from);
        while ($current->lte($to)) {
            $key = $current->format('Y-m-d');
            $labels[] = $current->format('M d');
            $data[] = $dailyWorkouts[$key] ?? 0;
            $current->addDay();
        }

        // Most active members
        $topMembers = MemberWorkoutLog::whereHas('plan', function ($query) {
                $query->where('gym_id', $this->gymId);
            })
            ->whereBetween('workout_date', [$from, $to])
            ->join(
                'member_exercise_plans',
                'member_workout_logs.member_exercise_plan_id',
                '=',
                'member_exercise_plans.id'
            )
            ->join('members', 'member_exercise_plans.member_id', '=', 'members.id')
            ->select('members.user_id', DB::raw('COUNT(*) as workout_count'))
            ->groupBy('members.user_id')
            ->orderByDesc('workout_count')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $user = \App\Models\User::find($row->user_id);

                return [
                    'name' => $user?->name ?? 'Unknown',
                    'count' => $row->workout_count,
                ];
            });

        // Diet log compliance (last 30 days)
       $dietLogs = MemberDietLog::whereHas('dietPlan', function ($query) {
                $query->where('gym_id', $this->gymId);
            })
            ->whereBetween('log_date', [$from, $to])
            ->count();

        $totalWorkouts = array_sum($data);

        return [
            'daily_workouts' => ['labels' => $labels, 'data' => $data],
            'top_members' => $topMembers,
            'total_workouts' => $totalWorkouts,
            'total_diet_logs' => $dietLogs,
        ];
    }

    // ─── HELPERS ─────────────────────────────────────────

    protected function getStatusColors(array $codes): array
    {
        $map = [
            'active' => '#22c55e',
            'expired' => '#ef4444',
            'frozen' => '#3b82f6',
            'cancelled' => '#6b7280',
            'pending' => '#f59e0b',
        ];

        return array_map(fn($code) => $map[$code] ?? '#9ca3af', $codes);
    }
}
