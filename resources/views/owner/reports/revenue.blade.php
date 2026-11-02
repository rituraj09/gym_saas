<x-owner-layout>
    <x-slot name="title">Revenue Report</x-slot>

    {{-- Sub Navigation --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
        <a href="{{ route('owner.reports.overview') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
            Overview
        </a>
        <a href="{{ route('owner.reports.revenue') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-indigo-600 text-white">
            Revenue
        </a>
        <a href="{{ route('owner.reports.membership') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
            Memberships
        </a>
        <a href="{{ route('owner.reports.activity') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap min-h-[44px] flex items-center bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
            Activity
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('owner.reports.revenue') }}"
            class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="from" value="{{ request('from', $report['from']) }}"
                    class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="to" value="{{ request('to', $report['to']) }}"
                    class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Payment Method</label>
                <select name="method_id" class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
                    <option value="">All Methods</option>
                    @foreach ($paymentMethods as $m)
                        <option value="{{ $m->id }}" {{ request('method_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                    Filter
                </button>
                <a href="{{ route('owner.reports.revenue') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition min-h-[44px]">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-green-700 mt-1">${{ number_format($report['total'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Transactions</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $report['count'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Average Transaction</p>
            <p class="text-2xl font-bold text-indigo-700 mt-1">
                ${{ $report['count'] > 0 ? number_format($report['total'] / $report['count'], 2) : '0.00' }}
            </p>
        </div>
    </div>

    {{-- Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Revenue</h3>
        <div class="h-72">
            <canvas id="revenueBarChart"></canvas>
        </div>
    </div>

    {{-- By Payment Method --}}
    @if ($report['by_method']->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Revenue by Payment Method</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($report['by_method'] as $method)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ $method->name }}</span>
                        <span
                            class="text-sm font-semibold text-gray-900">${{ number_format($method->total, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @include('owner.reports.partials.chart-js')
    <script>
        new Chart(document.getElementById('revenueBarChart'), {
            type: 'bar',
            data: {
                labels: @json($report['chart']['labels']),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($report['chart']['data']),
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
    </script>
</x-owner-layout>
