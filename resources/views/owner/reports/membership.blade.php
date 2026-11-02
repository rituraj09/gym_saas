<x-owner-layout>
    <x-slot name="title">Membership Report</x-slot>

    {{-- Sub Navigation --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
        <a href="{{ route('owner.reports.overview') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
            Overview
        </a>
        <a href="{{ route('owner.reports.revenue') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
            Revenue
        </a>
        <a href="{{ route('owner.reports.membership') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-indigo-600 text-white">
            Memberships
        </a>
        <a href="{{ route('owner.reports.activity') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
            Activity
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Active Memberships</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $report['total_active'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Expiring in 7 Days</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $report['expiring_soon'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Expired This Month</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $report['expired_this_month'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">New This Month</p>
            <p class="text-2xl font-bold text-indigo-700 mt-1">
                {{ collect($report['new_trend']['data'])->last() ?? 0 }}
            </p>
        </div>
    </div>

    {{-- New Memberships Trend --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">New Memberships Over Time</h3>
        <div class="h-64">
            <canvas id="newMembershipsChart"></canvas>
        </div>
    </div>

    {{-- Status Breakdown Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Current Status Breakdown</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($report['status_breakdown'] as $status)
                @php
                    $sColors = [
                        'active' => 'bg-green-100 text-green-800',
                        'expired' => 'bg-red-100 text-red-800',
                        'frozen' => 'bg-blue-100 text-blue-800',
                        'cancelled' => 'bg-gray-100 text-gray-800',
                        'pending' => 'bg-yellow-100 text-yellow-800',
                    ];
                    $sClass = $sColors[$status->code] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <div class="px-5 py-3 flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sClass }}">
                        {{ ucfirst($status->name) }}
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $status->count }}</span>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-gray-400 text-sm">No membership data yet.</div>
            @endforelse
        </div>
    </div>

    @include('owner.reports.partials.chart-js')
    <script>
        new Chart(document.getElementById('newMembershipsChart'), {
            type: 'bar',
            data: {
                labels: @json($report['new_trend']['labels']),
                datasets: [{
                    label: 'New Memberships',
                    data: @json($report['new_trend']['data']),
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: '#22c55e',
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
</x-owner-layout>
