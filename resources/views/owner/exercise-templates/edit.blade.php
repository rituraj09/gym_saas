<x-owner-layout>
    <div class="mb-6">
        <a href="{{ route('owner.exercise-templates.show', $template) }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Template
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Exercise Template</h1>
        <p class="mt-1 text-sm text-gray-600">Update the template details and exercises below.</p>
    </div>

    <form method="POST" action="{{ route('owner.exercise-templates.update', $template) }}" x-data="templateForm(@js(
    $template->items
        ->map(
            fn($item) => [
                'exercise_id' => $item->exercise_id,
                'sets' => $item->sets,
                'reps' => $item->reps,
                'rest_seconds' => $item->rest_seconds,
                'weight_kg' => $item->weight_kg ?? '',
                'notes' => $item->notes ?? '',
            ],
        )
        ->values(),
))"
        class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Template Details --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Template Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $template->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Muscle Group</label>
                    <select name="target_muscle_group_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                        <option value="">-- Any / Full Body --</option>
                        @foreach ($muscleGroups as $mg)
                            <option value="{{ $mg->id }}" @selected(old('target_muscle_group_id', $template->target_muscle_group_id) == $mg->id)>{{ $mg->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Duration (minutes)</label>
                    <input type="number" name="estimated_duration_minutes"
                        value="{{ old('estimated_duration_minutes', $template->estimated_duration_minutes) }}"
                        min="1" max="300"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                </div>
            </div>
        </div>

        {{-- Exercise Items --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Exercises <span class="text-red-500">*</span></h2>
                <button type="button" @click="addItem()"
                    class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    <x-icon name="plus-circle" class="w-4 h-4 mr-1" /> Add Exercise
                </button>
            </div>

            @error('items')
                <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
            @enderror

            <div x-show="items.length === 0" class="text-center py-8 text-gray-400">
                <x-icon name="fire" class="w-10 h-10 mx-auto mb-2" />
                <p class="text-sm">No exercises added yet. Click "Add Exercise" to start.</p>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-700" x-text="'Exercise #' + (index + 1)"></span>
                            <button type="button" @click="removeItem(index)"
                                class="text-red-500 hover:text-red-700 min-h-[44px] min-w-[44px] flex items-center justify-center">
                                <x-icon name="x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                            <div class="lg:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Exercise</label>
                                <select :name="'items[' + index + '][exercise_id]'" x-model="item.exercise_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                                    <option value="">Select exercise...</option>
                                    @foreach ($exercises as $ex)
                                        <option value="{{ $ex->id }}">{{ $ex->name }}
                                            ({{ $ex->muscleGroup->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Sets</label>
                                <input type="number" :name="'items[' + index + '][sets]'" x-model="item.sets"
                                    min="1" max="50" required
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Reps</label>
                                <input type="number" :name="'items[' + index + '][reps]'" x-model="item.reps"
                                    min="1" max="100" required
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Rest (sec)</label>
                                <input type="number" :name="'items[' + index + '][rest_seconds]'"
                                    x-model="item.rest_seconds" min="0" max="600"
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Weight (kg)</label>
                                <input type="number" :name="'items[' + index + '][weight_kg]'" x-model="item.weight_kg"
                                    min="0" max="999" step="0.5" placeholder="Optional"
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                            </div>
                        </div>
                        <div class="mt-2">
                            <input type="text" :name="'items[' + index + '][notes]'" x-model="item.notes"
                                placeholder="Optional notes"
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
            <a href="{{ route('owner.exercise-templates.show', $template) }}"
                class="w-full sm:w-auto text-center px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">
                Cancel
            </a>
            <button type="submit"
                class="w-full sm:w-auto px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="check-circle" class="w-4 h-4 inline mr-1" />
                Update Template
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            function templateForm(existingItems) {
                return {
                    items: existingItems.length > 0 ? existingItems : [],
                    addItem() {
                        this.items.push({
                            exercise_id: '',
                            sets: 3,
                            reps: 12,
                            rest_seconds: 60,
                            weight_kg: '',
                            notes: ''
                        });
                    },
                    removeItem(index) {
                        this.items.splice(index, 1);
                    }
                };
            }
        </script>
    @endpush
</x-owner-layout>
