<x-admin-layout>
    <x-slot name="title">All Gyms</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">All Gyms</h1>
        <p class="text-sm text-gray-500 mt-1">View and manage all gyms on the platform</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.gyms.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, code, city..."
                    class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Gym Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
                    <option value="">All</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->code }}" {{ request('status') === $s->code ? 'selected' : '' }}>
                            {{ ucfirst($s->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Subscription</label>
                <select name="subscription" class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
                    <option value="">All</option>
                    <option value="active" {{ request('subscription') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="trial" {{ request('subscription') === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="expired" {{ request('subscription') === 'expired' ? 'selected' : '' }}>Expired
                    </option>
                    <option value="none" {{ request('subscription') === 'none' ? 'selected' : '' }}>No Subscription
                    </option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                    Filter
                </button>
                <a href="{{ route('admin.gyms.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition min-h-[44px]">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($gyms->count() > 0)
            {{-- <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Gym</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Owner</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">City</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Members</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Trainers</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Subscription</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($gyms as $gym)
                            @php
                                $sub = $gym->activeSubscription;
                                $subCode = $sub?->status?->code ?? 'none';
                                $subColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'trial' => 'bg-blue-100 text-blue-800',
                                    'expired' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                    'suspended' => 'bg-yellow-100 text-yellow-800',
                                    'none' => 'bg-gray-50 text-gray-400',
                                ];
                                $subClass = $subColors[$subCode] ?? 'bg-gray-100 text-gray-800';
                                $gymStatusCode = $gym->status?->code ?? 'unknown';
                                $gymStatusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'suspended' => 'bg-red-100 text-red-800',
                                ];
                                $gymStatusClass = $gymStatusColors[$gymStatusCode] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $gym->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $gym->code }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $gym->owners->first()?->user?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $gym->city ?? '—' }}</td>
                                <td class="px-4 py-3 text-center text-gray-700 font-medium">{{ $gym->members_count }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 font-medium">{{ $gym->trainers_count }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $subClass }}">
                                        {{ $sub ? $sub->saasPlan?->name . ' (' . ucfirst($subCode) . ')' : 'None' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $gymStatusClass }}">
                                        {{ ucfirst($gymStatusCode) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.gyms.show', $gym) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg text-sm font-medium min-h-[44px]">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.gym-subscriptions.index', ['gym_id' => $gym->id]) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-green-600 hover:bg-green-50 rounded-lg text-sm font-medium min-h-[44px]">
                                            <x-icon name="credit-card" class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.gym-module-access.index', $gym) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-purple-600 hover:bg-purple-50 rounded-lg text-sm font-medium min-h-[44px]">
                                            <x-icon name="cog" class="w-4 h-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> --}}

            {{-- Mobile Cards --}}
            <div class="block divide-y divide-gray-100">
                @foreach ($gyms as $gym)
                    @php
                        $sub = $gym->activeSubscription;
                        $subCode = $sub?->status?->code ?? 'none';
                        $subColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'trial' => 'bg-blue-100 text-blue-800',
                            'expired' => 'bg-red-100 text-red-800',
                            'none' => 'bg-gray-50 text-gray-400',
                        ];
                        $subClass = $subColors[$subCode] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $gym->name }}</p>
                                <p class="text-xs text-gray-500">{{ $gym->code }} · {{ $gym->city ?? 'N/A' }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $subClass }}">
                                {{ $sub ? $sub->saasPlan?->name : 'No Plan' }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mb-3">
                            {{ $gym->members_count }} members · {{ $gym->trainers_count }} trainers ·
                            Owner: {{ $gym->owners->first()?->user?->name ?? '—' }}
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.gyms.show', $gym) }}"
                                class="flex-1 text-center px-3 py-2 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg min-h-[44px] flex items-center justify-center gap-1">
                                <x-icon name="eye" class="w-4 h-4" /> View
                            </a>
                            <a href="{{ route('admin.gym-module-access.index', $gym) }}"
                                class="flex-1 text-center px-3 py-2 bg-purple-50 text-purple-700 text-sm font-medium rounded-lg min-h-[44px] flex items-center justify-center gap-1">
                                <x-icon name="cog" class="w-4 h-4" /> Modules
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-4 py-3 border-t border-gray-200">
                {{ $gyms->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <x-icon name="building-office" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-500 font-medium">No gyms found</p>
            </div>
        @endif

    </div>
</x-admin-layout>
