<x-owner-layout>
    <x-slot name="title">Reports Overview</x-slot>

    {{-- Sub Navigation --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
        <a href="{{ route('owner.reports.overview') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center
                  {{ request()->routeIs('owner.reports.overview') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Overview
        </a>
        <a href="{{ route('owner.reports.revenue') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center
                  {{ request()->routeIs('owner.reports.revenue') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Revenue
        </a>
        <a href="{{ route('owner.reports.membership') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center
                  {{ request()->routeIs('owner.reports.membership') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Memberships
        </a>
        <a href="{{ route('owner.reports.activity') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center
                  {{ request()->routeIs('owner.reports.activity') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Activity
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Revenue (This Month)</p>
            <p class="text-xl font-bold text-green-700 mt-1">${{ number_format($summary['revenue_this_month'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">
                Last month: ${{ number_format($summary['revenue_last_month'], 2) }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Members</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $summary['total_members'] }}</p>
            <p class="text-xs text-green-600 mt-1">+{{ $summary['new_members_this_month'] }} this month</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Active Memberships</p>
            <p class="text-xl font-bold text-indigo-700 mt-1">{{ $summary['active_memberships'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $summary['total_trainers'] }} trainers</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Workouts (This Week)</p>
            <p class="text-xl font-bold text-orange-600 mt-1">{{ $summary['workouts_this_week'] }}</p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Revenue Trend --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Revenue Trend (Last 12 Months)</h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Membership Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Membership Status</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="membershipChart"></canvas>
            </div>
        </div>
    </div>

    @include('owner.reports.partials.chart-js')
    <script>
        // Revenue Trend
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: @json($revenueTrend['labels']),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($revenueTrend['data']),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#6366f1',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => '$' + v
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });

        // Membership Breakdown
        new Chart(document.getElementById('membershipChart'), {
            type: 'doughnut',
            data: {
                labels: @json($membershipBreakdown['labels']),
                datasets: [{
                    data: @json($membershipBreakdown['data']),
                    backgroundColor: @json($membershipBreakdown['colors']),
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    </script>
</x-owner-layout>
