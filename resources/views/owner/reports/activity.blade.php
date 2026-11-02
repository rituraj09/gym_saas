<x-owner-layout>
    <x-slot name="title">Activity Report</x-slot>

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
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
            Memberships
        </a>
        <a href="{{ route('owner.reports.activity') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-indigo-600 text-white">
            Activity
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('owner.reports.activity') }}"
            class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="from" value="{{ request('from', now()->subDays(30)->format('Y-m-d')) }}"
                    class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                    class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                    Filter
                </button>
                <a href="{{ route('owner.reports.activity') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition min-h-[44px]">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <x-icon name="fire" class="w-5 h-5 text-orange-600" />
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Workouts</p>
                    <p class="text-xl font-bold text-gray-900">{{ $report['total_workouts'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <x-icon name="heart" class="w-5 h-5 text-green-600" />
                </div>
                <div>
                    <p class="text-xs text-gray-500">Diet Logs</p>
                    <p class="text-xl font-bold text-gray-900">{{ $report['total_diet_logs'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Daily Workout Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Daily Workout Logs</h3>
        <div class="h-64">
            <canvas id="dailyWorkoutsChart"></canvas>
        </div>
    </div>

    {{-- Top Active Members --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Most Active Members</h3>
        </div>
        @if ($report['top_members']->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach ($report['top_members'] as $index => $member)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-sm font-medium text-gray-900">{{ $member['name'] }}</span>
                        </div>
                        <span class="text-sm text-gray-500">{{ $member['count'] }} workouts</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-5 py-8 text-center text-gray-400 text-sm">No workout data in this period.</div>
        @endif
    </div>

    @include('owner.reports.partials.chart-js')
    <script>
        new Chart(document.getElementById('dailyWorkoutsChart'), {
            type: 'bar',
            data: {
                labels: @json($report['daily_workouts']['labels']),
                datasets: [{
                    label: 'Workouts',
                    data: @json($report['daily_workouts']['data']),
                    backgroundColor: 'rgba(249, 115, 22, 0.7)',
                    borderColor: '#f97316',
                    borderWidth: 1,
                    borderRadius: 4,
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
                            maxRotation: 90,
                            autoSkip: true,
                            maxTicksLimit: 15
                        }
                    }
                }
            }
        });
    </script>
</x-owner-layout>
