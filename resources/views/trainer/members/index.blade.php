@php
    use Illuminate\Support\Facades\Crypt;
@endphp
<x-trainer-layout>
    <x-slot name="headerTitle">All Members</x-slot>

    <div class="space-y-6">
        <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 flex items-start gap-3">
            <x-icon name="information-circle" class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-indigo-800">
                <p class="font-semibold">All gym members are shown below.</p>
                <p class="mt-0.5">Members with the <span
                        class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-semibold">Assigned
                        to you</span> badge are specifically assigned to you. You can create exercise and diet plans for
                    any member.</p>
            </div>
        </div>

        @if ($members->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
                <x-icon name="users" class="w-12 h-12 mx-auto text-gray-300 mb-4" />
                <p class="text-lg font-semibold text-gray-600">No members in your gym yet</p>
                <p class="text-sm text-gray-400 mt-1">Members will appear here once the gym owner adds them.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($members as $member)
                    @php $isAssigned = in_array($member->id, $assignedIds); @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-11 h-11 rounded-full {{ $isAssigned ? 'bg-purple-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                                <span
                                    class="{{ $isAssigned ? 'text-purple-700' : 'text-gray-600' }} font-bold text-sm">{{ strtoupper(substr($member->user?->name ?? '?', 0, 2)) }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $member->user?->name }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ $member->user?->email }}</p>
                            </div>
                        </div>

                        @if ($isAssigned)
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 rounded-lg text-xs font-semibold">
                                    <x-icon name="check-badge" class="w-3.5 h-3.5" />
                                    Assigned to you
                                </span>
                            </div>
                        @endif

                        <div class="mt-3 space-y-1.5 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Membership</span>
                                @if ($member->activeMembership)
                                    <span
                                        class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-green-100 text-green-700">Active</span>
                                @else
                                    <span
                                        class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-red-100 text-red-700">Inactive</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Exercise Plan</span>
                                <span
                                    class="{{ $member->activeExercisePlan ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                    {{ $member->activeExercisePlan ? '✓ Active' : 'None' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Diet Plan</span>
                                <span
                                    class="{{ $member->activeDietPlan ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                    {{ $member->activeDietPlan ? '✓ Active' : 'None' }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('trainer.members.show', Crypt::encryptString($member->id)) }}"
                            class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition min-h-[44px]">
                            <x-icon name="eye" class="w-4 h-4" />
                            View Details
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-trainer-layout>
