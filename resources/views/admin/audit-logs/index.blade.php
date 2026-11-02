<x-admin-layout>
    <x-slot name="headerTitle">Audit Log</x-slot>

    <div class="space-y-6">

        {{-- ── Filters ── --}}
        <form method="GET" action="{{ route('admin.audit-logs.index') }}"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">

                {{-- Gym --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Gym</label>
                    <select name="gym_id"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm min-h-[44px]">
                        <option value="">All Gyms</option>
                        @foreach ($gyms as $gym)
                            <option value="{{ $gym->id }}" @selected(request('gym_id') == $gym->id)>{{ $gym->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- User --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                    <select name="user_id"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm min-h-[44px]">
                        <option value="">All Users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Action --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                    <select name="action"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm min-h-[44px]">
                        <option value="">All Actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Model Type --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Model</label>
                    <select name="model_type"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm min-h-[44px]">
                        <option value="">All Models</option>
                        @foreach ($modelTypes as $type)
                            <option value="{{ $type }}" @selected(request('model_type') === $type)>{{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm min-h-[44px]">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm min-h-[44px]">
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition min-h-[44px]">
                    <x-icon name="funnel" class="w-4 h-4" />
                    Filter
                </button>
                <a href="{{ route('admin.audit-logs.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition min-h-[44px]">
                    Clear
                </a>
                <span class="text-sm text-gray-500 ml-auto">{{ $logs->total() }} records</span>
            </div>
        </form>

        {{-- ── Log Table ── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Date & Time
                            </th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">User</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Action</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Target</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">Details
                            </th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 hidden xl:table-cell">Gym</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 hidden xl:table-cell">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50/50 transition">
                                {{-- Date --}}
                                <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                    <div class="font-medium text-gray-900">{{ $log->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</div>
                                </td>

                                {{-- User --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($log->user)
                                        <span class="font-medium text-gray-900">{{ $log->user->name }}</span>
                                    @else
                                        <span class="text-gray-400 italic">System</span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $log->action_badge_class }}">
                                        <x-icon name="{{ $log->action_icon }}" class="w-3.5 h-3.5" />
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>

                                {{-- Target --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $log->model_label ?? '—' }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->model_name }} #{{ $log->model_id }}
                                    </div>
                                </td>

                                {{-- Details --}}
                                <td class="px-4 py-3 text-gray-600 hidden lg:table-cell max-w-xs truncate">
                                    {{ $log->changes_summary }}
                                </td>

                                {{-- Gym --}}
                                <td class="px-4 py-3 whitespace-nowrap text-gray-600 hidden xl:table-cell">
                                    {{ $log->gym?->name ?? '—' }}
                                </td>

                                {{-- IP --}}
                                <td class="px-4 py-3 whitespace-nowrap text-gray-400 text-xs hidden xl:table-cell">
                                    {{ $log->ip_address ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                    <x-icon name="document-text" class="w-10 h-10 mx-auto mb-3 text-gray-300" />
                                    <p class="font-medium">No audit log entries found</p>
                                    <p class="text-sm mt-1">Try adjusting your filters</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($logs->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>
</x-admin-layout>
