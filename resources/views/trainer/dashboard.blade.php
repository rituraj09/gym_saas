<x-trainer-layout>
    <x-slot name="headerTitle">Dashboard</x-slot>

    <div class="space-y-6">

        {{-- Welcome Section --}}
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-2xl p-6 text-white">
            <h1 class="text-2xl font-bold">
                Welcome, {{ Auth::user()->name }}!
            </h1>
            <p class="mt-1 text-indigo-100">
                Here's an overview of your gym members and plans.
            </p>
        </div>

        {{-- Dashboard Statistics --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total Gym Members --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <x-icon name="users" class="w-5 h-5 text-blue-600" />
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Gym Members</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $stats['total_gym_members'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- My Members --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                        <x-icon name="users" class="w-5 h-5 text-purple-600" />
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">My Members</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $stats['my_members'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Active Exercise Plans --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                        <x-icon name="clipboard-document" class="w-5 h-5 text-green-600" />
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Exercise Plans</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $stats['active_exercise'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Active Diet Plans --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                        <x-icon name="heart" class="w-5 h-5 text-orange-600" />
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Diet Plans</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $stats['active_diet'] }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- View Members --}}
            <a href="{{ route('trainer.members.index') }}"
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition flex items-center gap-3 min-h-[44px]">

                <x-icon name="users" class="w-5 h-5 text-indigo-600" />

                <span class="font-medium text-gray-900">
                    View All Members
                </span>
            </a>

            {{-- Create Exercise Plan --}}
            <a href="{{ route('trainer.exercise-plans.create') }}"
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition flex items-center gap-3 min-h-[44px]">

                <x-icon name="plus-circle" class="w-5 h-5 text-green-600" />

                <span class="font-medium text-gray-900">
                    Create Exercise Plan
                </span>
            </a>

            {{-- Create Diet Plan --}}
            <a href="{{ route('trainer.diet-plans.create') }}"
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition flex items-center gap-3 min-h-[44px]">

                <x-icon name="plus-circle" class="w-5 h-5 text-orange-600" />

                <span class="font-medium text-gray-900">
                    Create Diet Plan
                </span>
            </a>

        </div>

        {{-- Recent Exercise Plans --}}
        @if ($recentPlans->isNotEmpty())

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                    <h3 class="font-semibold text-gray-900">
                        Your Recent Exercise Plans
                    </h3>
                </div>

                <div class="divide-y divide-gray-100">

                    @foreach ($recentPlans as $plan)
                        <a href="{{ route('trainer.exercise-plans.show', Crypt::encryptString($plan->id)) }}"
                            class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition min-h-[44px]">

                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 truncate">
                                    {{ $plan->name }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    {{ $plan->member?->user?->name ?? '—' }}
                                    ·
                                    {{ $plan->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <span
                                class="ml-3 shrink-0 px-2.5 py-1 rounded-lg text-xs font-semibold
                                {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">

                                {{ $plan->is_active ? 'Active' : 'Inactive' }}

                            </span>

                        </a>
                    @endforeach

                </div>

            </div>

        @endif

    </div>
</x-trainer-layout>
