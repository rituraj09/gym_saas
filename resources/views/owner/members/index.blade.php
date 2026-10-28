<x-owner-layout>
    <x-slot name="title">Members</x-slot>
    <x-slot name="headerTitle">Members</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">All Members</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $members->total() }} member(s) found</p>
        </div>
        <a href="{{ route('owner.members.create') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Member
        </a>
    </div>

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
                class="px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition min-h-[44px]">Filter</button>
            @if (request()->hasAny(['search', 'status']))
                <a href="{{ route('owner.members.index') }}"
                    class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition text-center min-h-[44px]">Clear</a>
            @endif
        </div>
    </form>

    @if ($members->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <x-icon name="user" class="w-8 h-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No members yet</h3>
            <p class="text-sm text-gray-500 mb-5">Add your first member to get started.</p>
            <a href="{{ route('owner.members.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Member
            </a>
        </div>
    @else
        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Member</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Code</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Gender</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Blood Group</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Joined</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($members as $member)
                        @php $isActive = $member->user->accountStatus?->code === 'active'; @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($member->profile_photo)
                                        <img src="{{ asset('storage/' . $member->profile_photo) }}"
                                            class="w-9 h-9 rounded-full object-cover">
                                    @else
                                        <div
                                            class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <span
                                                class="text-blue-700 font-semibold text-sm">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $member->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $member->user->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $member->member_code }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $member->gender?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $member->bloodGroup?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $member->joining_date?->format('M d, Y') }}</td>
                            <td class="px-5 py-3">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('owner.members.show', $member) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-3">
            @foreach ($members as $member)
                @php $isActive = $member->user->accountStatus?->code === 'active'; @endphp
                <a href="{{ route('owner.members.show', $member) }}"
                    class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-sm transition">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            @if ($member->profile_photo)
                                <img src="{{ asset('storage/' . $member->profile_photo) }}"
                                    class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span
                                        class="text-blue-700 font-semibold">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900">{{ $member->user->name }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $member->member_code }}</p>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 flex gap-4">
                        <span>{{ $member->gender?->name ?? '—' }}</span>
                        <span>Joined {{ $member->joining_date?->format('M d, Y') }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $members->links() }}
        </div>
    @endif
</x-owner-layout>
