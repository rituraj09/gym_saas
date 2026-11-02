<x-admin-layout>
    <x-slot name="title">All Users</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">All Users</h1>
        <p class="text-sm text-gray-500 mt-1">View and manage all platform users</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, phone..."
                    class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                <select name="role" class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
                    <option value="">All Roles</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->code }}" {{ request('role') === $r->code ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
                    <option value="">All</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->code }}" {{ request('status') === $s->code ? 'selected' : '' }}>
                            {{ ucfirst($s->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                    Filter
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition min-h-[44px]">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($users->count() > 0)
            {{-- <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">User</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Email</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Role(s)</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Gym</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Joined</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            @php
                                $statusCode = $user->accountStatus?->code ?? 'unknown';
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-gray-100 text-gray-800',
                                    'suspended' => 'bg-red-100 text-red-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                ];
                                $statusClass = $statusColors[$statusCode] ?? 'bg-gray-100 text-gray-800';
                                $gym = $user->gymOwner?->gym ?? ($user->trainer?->gym ?? $user->member?->gym);
                                $roleNames = $user->roles
                                    ->pluck('name')
                                    ->map(fn($n) => ucfirst(str_replace('_', ' ', $n)))
                                    ->implode(', ');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs text-gray-600">{{ $roleNames ?: '—' }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $gym?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ ucfirst($statusCode) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg text-sm font-medium min-h-[44px]">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                                            onsubmit="return confirm('{{ $user->isActive() ? 'Deactivate' : 'Activate' }} this user?')">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 {{ $user->isActive() ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg text-sm font-medium min-h-[44px]">
                                                <x-icon name="{{ $user->isActive() ? 'x-mark' : 'check-circle' }}"
                                                    class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> --}}

            {{-- Mobile Cards --}}
            <div class="block divide-y divide-gray-100">
                @foreach ($users as $user)
                    @php
                        $statusCode = $user->accountStatus?->code ?? 'unknown';
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'inactive' => 'bg-gray-100 text-gray-800',
                            'suspended' => 'bg-red-100 text-red-800',
                        ];
                        $statusClass = $statusColors[$statusCode] ?? 'bg-gray-100 text-gray-800';
                        $roleNames = $user->roles
                            ->pluck('name')
                            ->map(fn($n) => ucfirst(str_replace('_', ' ', $n)))
                            ->implode(', ');
                    @endphp
                    <a href="{{ route('admin.users.show', $user) }}" class="block p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ ucfirst($statusCode) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $user->email }} · {{ $roleNames }}</p>
                    </a>
                @endforeach
            </div>

            <div class="px-4 py-3 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <x-icon name="users" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-500 font-medium">No users found</p>
            </div>
        @endif
    </div>
</x-admin-layout>
