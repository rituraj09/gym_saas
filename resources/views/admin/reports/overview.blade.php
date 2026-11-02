<x-admin-layout>
    <x-slot name="title">Platform Reports</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Platform Reports</h1>
        <p class="text-sm text-gray-500 mt-1">Overview of platform-wide metrics</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Gyms</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['total_gyms'] }}</p>
            <p class="text-xs text-green-600 mt-1">{{ $summary['active_gyms'] }} active</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-green-700 mt-1">${{ number_format($summary['total_revenue'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">This month: ${{ number_format($summary['revenue_this_month'], 2) }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Active Subscriptions</p>
            <p class="text-2xl font-bold text-indigo-700 mt-1">{{ $summary['active_subscriptions'] }}</p>
            <p class="text-xs text-yellow-600 mt-1">{{ $summary['expiring_subscriptions'] }} expiring soon</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Users</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['total_users'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $summary['total_members'] }} members ·
                {{ $summary['total_trainers'] }} trainers</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Revenue Trend --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Platform Revenue (Last 12 Months)</h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Subscription Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Active Subscriptions by Plan</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="subscriptionChart"></canvas>
            </div>
        </div>

        {{-- Gym Growth --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">New Gyms Registered (Last 12 Months)</h3>
            <div class="h-64">
                <canvas id="gymGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: @json($revenueTrend['labels']),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($revenueTrend['data']),
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#22c55e',
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

        new Chart(document.getElementById('subscriptionChart'), {
            type: 'doughnut',
            data: {
                labels: @json($subscriptionBreakdown['labels']),
                datasets: [{
                    data: @json($subscriptionBreakdown['data']),
                    backgroundColor: ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'],
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

        new Chart(document.getElementById('gymGrowthChart'), {
            type: 'bar',
            data: {
                labels: @json($gymGrowth['labels']),
                datasets: [{
                    label: 'New Gyms',
                    data: @json($gymGrowth['data']),
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    borderRadius: 6,
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
                            stepSize: 1
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
    </script>
</x-admin-layout>
