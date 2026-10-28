<x-owner-layout>
    <x-slot name="title">Assign Trainer</x-slot>
    <x-slot name="headerTitle">Assign Trainer</x-slot>

    <div class="max-w-3xl space-y-6">

        <!-- Current Assignment -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <x-icon name="user" class="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Current Trainer</h2>
                        <p class="text-xs text-gray-500">Member: {{ $member->user->name }} ({{ $member->member_code }})
                        </p>
                    </div>
                </div>
                @if ($currentTrainer)
                    <form method="POST" action="{{ route('owner.assignments.unassign', $member) }}"
                        onsubmit="return confirm('Remove the current trainer from this member?')">
                        @csrf
                        <button type="submit"
                            class="text-sm font-medium text-red-600 hover:text-red-800 px-3 py-1.5 rounded-lg hover:bg-red-50 transition min-h-[44px]">
                            Unassign
                        </button>
                    </form>
                @endif
            </header>
            <div class="p-5 sm:p-6">
                @if ($currentTrainer)
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <span
                                class="text-green-700 font-bold text-lg">{{ strtoupper(substr($currentTrainer->user->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $currentTrainer->user->name }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ $currentTrainer->trainer_code }} ·
                                {{ $currentTrainer->specialization ?? 'No specialization' }}</p>
                        </div>
                        <span
                            class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                        </span>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">Assigning a new trainer below will automatically close this
                        assignment and preserve it in history.</p>
                @else
                    <div class="text-center py-6">
                        <x-icon name="user" class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                        <p class="text-sm text-gray-500">No trainer currently assigned.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Assign New Trainer -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <x-icon name="users" class="w-5 h-5 text-green-600" />
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ $currentTrainer ? 'Reassign Trainer' : 'Assign Trainer' }}</h2>
                    <p class="text-xs text-gray-500">Select a trainer and start date.</p>
                </div>
            </header>

            <form method="POST" action="{{ route('owner.assignments.store') }}" class="p-5 sm:p-6">
                @csrf
                <input type="hidden" name="member_id" value="{{ $member->id }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Trainer Select -->
                    <div class="sm:col-span-2">
                        <label for="trainer_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Select Trainer <span class="text-red-500">*</span>
                        </label>
                        @if ($trainers->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                                No active trainers available. <a href="{{ route('owner.trainers.create') }}"
                                    class="underline font-medium">Add a trainer first</a>.
                            </div>
                        @else
                            <select name="trainer_id" id="trainer_id" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('trainer_id') border-red-500 @enderror">
                                <option value="">— Choose a trainer —</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}"
                                        {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}
                                        {{ $currentTrainer && $currentTrainer->id === $trainer->id ? 'disabled' : '' }}>
                                        {{ $trainer->user->name }} ({{ $trainer->trainer_code }})
                                        {{ $currentTrainer && $currentTrainer->id === $trainer->id ? '— Currently Assigned' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('trainer_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date"
                            value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row-reverse gap-3">
                    <button type="submit" {{ $trainers->isEmpty() ? 'disabled' : '' }}
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-icon name="check-circle" class="w-5 h-5" />
                        {{ $currentTrainer ? 'Reassign Trainer' : 'Assign Trainer' }}
                    </button>
                    <a href="{{ route('owner.members.show', $member) }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition min-h-[44px]">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Assignment History -->
        @if ($history->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center">
                        <x-icon name="calendar" class="w-5 h-5 text-gray-500" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Assignment History</h2>
                        <p class="text-xs text-gray-500">{{ $history->count() }} record(s)</p>
                    </div>
                </header>

                <div class="divide-y divide-gray-100">
                    @foreach ($history as $assignment)
                        <div class="px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <span
                                        class="text-gray-600 font-semibold text-xs">{{ strtoupper(substr($assignment->trainer->user->name, 0, 1)) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $assignment->trainer->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $assignment->start_date->format('M d, Y') }} —
                                        {{ $assignment->end_date ? $assignment->end_date->format('M d, Y') : 'Present' }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium self-start sm:self-auto
                                @if ($assignment->status->code === 'active') bg-green-50 text-green-700 border border-green-200
                                @elseif ($assignment->status->code === 'transferred') bg-blue-50 text-blue-700 border border-blue-200
                                @else bg-gray-100 text-gray-600 border border-gray-200 @endif">
                                {{ ucfirst($assignment->status->name) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <a href="{{ route('owner.members.show', $member) }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-indigo-600 transition">
            ← Back to Member Details
        </a>
    </div>
</x-owner-layout>
