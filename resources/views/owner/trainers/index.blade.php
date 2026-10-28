<x-owner-layout>
    <x-slot name="title">Trainers</x-slot>
    <x-slot name="headerTitle">Trainers</x-slot>

    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">All Trainers</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $trainers->total() }} trainer(s) found</p>
        </div>
        <a href="{{ route('owner.trainers.create') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Trainer
        </a>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, email, phone, or code..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 text-sm">
            </div>
            <select name="status"
                class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 text-sm min-w-[140px]">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit"
                class="px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition min-h-[44px]">
                Filter
            </button>
            @if (request()->hasAny(['search', 'status']))
                <a href="{{ route('owner.trainers.index') }}"
                    class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition text-center min-h-[44px]">
                    Clear
                </a>
            @endif
        </div>
    </form>

    <!-- Trainers List -->
    @if ($trainers->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <x-icon name="users" class="w-8 h-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No trainers yet</h3>
            <p class="text-sm text-gray-500 mb-5">Add your first trainer to get started.</p>
            <a href="{{ route('owner.trainers.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Trainer
            </a>
        </div>
    @else
        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Trainer</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Code</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Specialization</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Experience</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($trainers as $trainer)
                        @php $isActive = $trainer->user->accountStatus?->code === 'active'; @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <span
                                            class="text-green-700 font-semibold text-sm">{{ strtoupper(substr($trainer->user->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $trainer->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $trainer->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $trainer->trainer_code }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $trainer->specialization ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $trainer->experience_years }} yr(s)</td>
                            <td class="px-5 py-3">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('owner.trainers.show', $trainer) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-3">
            @foreach ($trainers as $trainer)
                @php $isActive = $trainer->user->accountStatus?->code === 'active'; @endphp
                <a href="{{ route('owner.trainers.show', $trainer) }}"
                    class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-sm transition">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <span
                                    class="text-green-700 font-semibold">{{ strtoupper(substr($trainer->user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $trainer->user->name }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $trainer->trainer_code }}</p>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 flex gap-4">
                        <span>{{ $trainer->specialization ?? 'No specialization' }}</span>
                        <span>{{ $trainer->experience_years }} yr(s)</span>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $trainers->links() }}
        </div>
    @endif
</x-owner-layout>
