<x-admin-layout>
    <x-slot name="title">{{ $user->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-2">
            <x-icon name="arrow-left" class="w-4 h-4" /> Back to All Users
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">User Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                        <p class="text-gray-900 mt-0.5">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Phone</p>
                        <p class="text-gray-900 mt-0.5">{{ $user->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Roles</p>
                        <p class="text-gray-900 mt-0.5">
                            {{ $user->roles->pluck('name')->map(fn($n) => ucfirst(str_replace('_', ' ', $n)))->implode(', ') ?: 'None' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Account Status</p>
                        @php
                            $sc = $user->accountStatus?->code ?? 'unknown';
                            $scColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'inactive' => 'bg-gray-100 text-gray-800',
                                'suspended' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span
                            class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $scColors[$sc] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($sc) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Joined</p>
                        <p class="text-gray-900 mt-0.5">{{ $user->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Last Updated</p>
                        <p class="text-gray-900 mt-0.5">{{ $user->updated_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Gym Association --}}
            @php $gym = $user->gymOwner?->gym ?? $user->trainer?->gym ?? $user->member?->gym; @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Gym Association</h3>
                @if ($gym)
                    <a href="{{ route('admin.gyms.show', $gym) }}"
                        class="block hover:bg-gray-50 -mx-2 px-2 py-2 rounded-lg transition">
                        <p class="font-medium text-indigo-700">{{ $gym->name }}</p>
                        <p class="text-xs text-gray-500">{{ $gym->code }} · {{ $gym->city ?? '' }}</p>
                    </a>
                @else
                    <p class="text-sm text-gray-400">Not associated with any gym.</p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Actions</h3>
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                    onsubmit="return confirm('{{ $user->isActive() ? 'Deactivate' : 'Activate' }} this user?')">
                    @csrf
                    <button type="submit"
                        class="w-full px-4 py-2.5 text-sm font-medium rounded-lg min-h-[44px] transition
                                   {{ $user->isActive()
                                       ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                       : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                        {{ $user->isActive() ? 'Deactivate User' : 'Activate User' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
