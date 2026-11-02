<x-trainer-layout>
    <x-slot name="headerTitle">{{ $member->user?->name ?? 'Member' }}</x-slot>

    <div class="space-y-6">
        <a href="{{ route('trainer.members.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition min-h-[44px]">
            <x-icon name="arrow-left" class="w-4 h-4" />
            Back to Members
        </a>

        {{-- Profile Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-4 mb-6">
                <div
                    class="w-14 h-14 rounded-full {{ $isAssigned ? 'bg-purple-100' : 'bg-gray-100' }} flex items-center justify-center">
                    <span
                        class="{{ $isAssigned ? 'text-purple-700' : 'text-gray-600' }} font-bold text-lg">{{ strtoupper(substr($member->user?->name ?? '?', 0, 2)) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $member->user?->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $member->user?->email }} ·
                        {{ $member->user?->phone ?? 'No phone' }}</p>
                    @if ($isAssigned)
                        <span
                            class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-purple-100 text-purple-700 rounded-lg text-xs font-semibold">Assigned
                            to you</span>
                    @else
                        <span
                            class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-gray-100 text-gray-500 rounded-lg text-xs font-semibold">No
                            trainer assigned</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><span class="text-gray-500">Gender</span>
                    <p class="font-medium text-gray-900">{{ $member->gender?->name ?? '—' }}</p>
                </div>
                <div><span class="text-gray-500">Blood Group</span>
                    <p class="font-medium text-gray-900">{{ $member->bloodGroup?->name ?? '—' }}</p>
                </div>
                <div><span class="text-gray-500">Date of Birth</span>
                    <p class="font-medium text-gray-900">
                        {{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('M d, Y') : '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Membership --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Membership</h3>
            @if ($member->activeMembership)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div><span class="text-gray-500">Plan</span>
                        <p class="font-medium text-gray-900">
                            {{ $member->activeMembership->plan?->name ?? '—' }}</p>
                    </div>
                    <div><span class="text-gray-500">Valid Till</span>
                        <p class="font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($member->activeMembership->end_date)->format('M d, Y') }}</p>
                    </div>
                    <div><span class="text-gray-500">Status</span><span
                            class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-green-100 text-green-700">{{ $member->activeMembership->membershipStatus?->name ?? 'Active' }}</span>
                    </div>
                </div>
            @else
                <p class="text-gray-400 text-sm">No active membership</p>
            @endif
        </div>

        {{-- Current Plans --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Exercise Plan</h3>
                @if ($member->activeExercisePlan)
                    <p class="font-medium text-gray-900">{{ $member->activeExercisePlan->name }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $member->activeExercisePlan->workoutDays->count() }}
                        days/week</p>

                    <a href="{{ route('trainer.exercise-plans.show', Crypt::encryptString($member->activeExercisePlan->id)) }}"
                        class="mt-3 inline-flex items-center gap-1 text-sm text-indigo-600 font-medium min-h-[44px]">View
                        Plan →</a>
                @else
                    <p class="text-gray-400 text-sm">No active plan</p>
                    <a href="{{ route('trainer.exercise-plans.create') }}"
                        class="mt-3 inline-flex items-center gap-1 text-sm text-indigo-600 font-medium min-h-[44px]">+
                        Create Plan</a>
                @endif
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Diet Plan</h3>
                @if ($member->activeDietPlan)
                    <p class="font-medium text-gray-900">{{ $member->activeDietPlan->plan_name }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $member->activeDietPlan->total_calories ?? '—' }}
                        calories/day</p>
                    <a href="{{ route('trainer.diet-plans.show', $member->activeDietPlan) }}"
                        class="mt-3 inline-flex items-center gap-1 text-sm text-indigo-600 font-medium min-h-[44px]">View
                        Plan →</a>
                @else
                    <p class="text-gray-400 text-sm">No active plan</p>
                    <a href="{{ route('trainer.diet-plans.create') }}"
                        class="mt-3 inline-flex items-center gap-1 text-sm text-indigo-600 font-medium min-h-[44px]">+
                        Create Plan</a>
                @endif
            </div>
        </div>
    </div>
</x-trainer-layout>
