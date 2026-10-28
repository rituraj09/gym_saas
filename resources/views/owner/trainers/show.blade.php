<x-owner-layout>
    <x-slot name="title">{{ $trainer->user->name }}</x-slot>
    <x-slot name="headerTitle">Trainer Details</x-slot>

    @php $isActive = $trainer->user->accountStatus?->code === 'active'; @endphp

    <div class="max-w-3xl space-y-6">
        <!-- Profile Header -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row items-start gap-5">
                @if ($trainer->profile_photo)
                    <img src="{{ asset('storage/' . $trainer->profile_photo) }}"
                        class="w-20 h-20 rounded-2xl object-cover border">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <span
                            class="text-green-700 font-bold text-2xl">{{ strtoupper(substr($trainer->user->name, 0, 1)) }}</span>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl font-bold text-gray-900">{{ $trainer->user->name }}</h2>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 font-mono mt-1">{{ $trainer->trainer_code }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $trainer->specialization ?? 'No specialization' }} ·
                        {{ $trainer->experience_years }} year(s) experience</p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="{{ route('owner.trainers.edit', $trainer) }}"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition min-h-[44px]">
                        <x-icon name="cog" class="w-4 h-4" /> Edit
                    </a>
                    <form method="POST" action="{{ route('owner.trainers.toggle-status', $trainer) }}"
                        onsubmit="return confirm('Are you sure you want to {{ $isActive ? 'deactivate' : 'activate' }} this trainer?')">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 {{ $isActive ? 'bg-red-50 text-red-700 hover:bg-red-100 border-red-200' : 'bg-green-50 text-green-700 hover:bg-green-100 border-green-200' }} border text-sm font-medium rounded-xl transition min-h-[44px]">
                            {{ $isActive ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Email</p>
                <p class="text-sm font-medium text-gray-900">{{ $trainer->user->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Phone</p>
                <p class="text-sm font-medium text-gray-900">{{ $trainer->user->phone }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Date of Birth</p>
                <p class="text-sm font-medium text-gray-900">{{ $trainer->date_of_birth?->format('M d, Y') ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Gender</p>
                <p class="text-sm font-medium text-gray-900">{{ $trainer->gender?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Blood Group</p>
                <p class="text-sm font-medium text-gray-900">{{ $trainer->bloodGroup?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Qualification</p>
                <p class="text-sm font-medium text-gray-900">{{ $trainer->qualification ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Joining Date</p>
                <p class="text-sm font-medium text-gray-900">{{ $trainer->joining_date?->format('M d, Y') }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-500 mb-0.5">Address</p>
                <p class="text-sm font-medium text-gray-900">{{ $trainer->address ?? '—' }}</p>
            </div>
        </div>

        <!-- Assigned Members Placeholder -->
        <!-- Assigned Members -->
        @php
            $assignedMembers = app(\App\Services\AssignmentService::class)->getTrainerMembers($trainer->gym, $trainer);
        @endphp
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Assigned Members</h3>
                    <p class="text-xs text-gray-500">{{ $assignedMembers->count() }} active member(s)</p>
                </div>
            </header>
            @if ($assignedMembers->isEmpty())
                <div class="p-8 text-center">
                    <x-icon name="users" class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                    <p class="text-sm text-gray-500">No members assigned yet.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($assignedMembers as $assignedMember)
                        <a href="{{ route('owner.members.show', $assignedMember) }}"
                            class="flex items-center gap-3 px-5 sm:px-6 py-3 hover:bg-gray-50 transition">
                            <div
                                class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span
                                    class="text-blue-700 font-semibold text-sm">{{ strtoupper(substr($assignedMember->user->name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $assignedMember->user->name }}
                                </p>
                                <p class="text-xs text-gray-500 font-mono">{{ $assignedMember->member_code }}</p>
                            </div>
                            <x-icon name="arrow-right" class="w-4 h-4 text-gray-400" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <a href="{{ route('owner.trainers.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-indigo-600 transition">
            ← Back to Trainers
        </a>
    </div>
</x-owner-layout>
