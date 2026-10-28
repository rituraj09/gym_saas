<x-owner-layout>
    <x-slot name="title">Memberships</x-slot>
    <x-slot name="headerTitle">Memberships</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">All Memberships</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $memberships->total() }} record(s)</p>
        </div>
        <a href="{{ route('owner.memberships.create') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Assign Membership
        </a>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by member name..."
                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 text-sm">
            <select name="status"
                class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 text-sm min-w-[150px]">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="expiring_soon" {{ request('status') === 'expiring_soon' ? 'selected' : '' }}>Expiring
                    Soon</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
            </select>
            <button type="submit"
                class="px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition min-h-[44px]">Filter</button>
        </div>
    </form>

    @if ($memberships->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <x-icon name="clipboard" class="w-8 h-8 text-gray-300 mx-auto mb-3" />
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No memberships yet</h3>
            <p class="text-sm text-gray-500 mb-5">Assign a membership plan to a member to get started.</p>
            <a href="{{ route('owner.memberships.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">Assign
                Membership</a>
        </div>
    @else
        <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Member</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Plan</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Period</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Price</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($memberships as $m)
                        @php
                            $statusColors = [
                                'active' => 'bg-green-50 text-green-700',
                                'expiring_soon' => 'bg-amber-50 text-amber-700',
                                'expired' => 'bg-red-50 text-red-700',
                                'upcoming' => 'bg-blue-50 text-blue-700',
                                'cancelled' => 'bg-gray-100 text-gray-600',
                            ];
                            $color = $statusColors[$m->status->code] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $m->member->user->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $m->plan->name }}</td>
                            <td class="px-5 py-3 text-gray-600 text-xs">{{ $m->start_date->format('M d, Y') }} —
                                {{ $m->end_date->format('M d, Y') }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ number_format($m->price, 2) }}</td>
                            <td class="px-5 py-3"><span
                                    class="px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">{{ $m->status->name }}</span>
                            </td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('owner.memberships.show', $m) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            @foreach ($memberships as $m)
                @php
                    $statusColors = [
                        'active' => 'bg-green-50 text-green-700',
                        'expiring_soon' => 'bg-amber-50 text-amber-700',
                        'expired' => 'bg-red-50 text-red-700',
                        'upcoming' => 'bg-blue-50 text-blue-700',
                        'cancelled' => 'bg-gray-100 text-gray-600',
                    ];
                    $color = $statusColors[$m->status->code] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <a href="{{ route('owner.memberships.show', $m) }}"
                    class="block bg-white rounded-xl border border-gray-200 p-4">
                    <div class="flex justify-between items-start mb-2">
                        <p class="font-semibold text-gray-900">{{ $m->member->user->name }}</p>
                        <span
                            class="px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">{{ $m->status->name }}</span>
                    </div>
                    <p class="text-sm text-gray-600">{{ $m->plan->name }} · {{ number_format($m->price, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $m->start_date->format('M d') }} —
                        {{ $m->end_date->format('M d, Y') }}</p>
                </a>
            @endforeach
        </div>
        <div class="mt-4">{{ $memberships->links() }}</div>
    @endif
</x-owner-layout>
