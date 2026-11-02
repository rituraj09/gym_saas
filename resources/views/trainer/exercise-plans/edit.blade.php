@php
    use Illuminate\Support\Facades\Crypt;
@endphp
<x-trainer-layout>
    <x-slot name="headerTitle">Edit Exercise Plan</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        <div>
            <a href="{{ route('trainer.exercise-plans.show', Crypt::encryptString($exercise_plan->id)) }}"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition min-h-[44px]">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Back to Exercise Plan
            </a>

            <div class="mt-4">
                <h2 class="text-2xl font-bold text-gray-900">Edit Exercise Plan</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Update the plan details and weekly workout schedule.
                </p>
            </div>
        </div>

        @include('trainer.exercise-plans._form', [
            'exercise_plan' => $exercise_plan,
            'members' => $members,
            'exerciseTemplates' => $exerciseTemplates,
        ])

    </div>
</x-trainer-layout>
