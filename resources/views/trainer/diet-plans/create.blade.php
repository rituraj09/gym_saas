<x-trainer-layout>
    <x-slot name="headerTitle">Create Diet Plan</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <a href="{{ route('trainer.diet-plans.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 min-h-[44px]">
            <x-icon name="arrow-left" class="w-4 h-4" />
            Back to Plans
        </a>

        <form method="POST" action="{{ route('trainer.diet-plans.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <x-icon name="cake" class="w-5 h-5 text-orange-500" />
                    Plan Details
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Member <span
                                class="text-red-500">*</span></label>
                        <select name="member_id" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 min-h-[44px]">
                            <option value="">Select Member</option>
                            @foreach ($members as $m)
                                <option value="{{ $m->id }}" @selected(old('member_id') == $m->id)>{{ $m->user?->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('member_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="plan_name" value="{{ old('plan_name') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 min-h-[44px]"
                            placeholder="e.g. Weight Loss Diet">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 min-h-[44px]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 min-h-[44px]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Calories/Day</label>
                        <input type="number" name="total_calories" value="{{ old('total_calories') }}" min="0"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 min-h-[44px]"
                            placeholder="e.g. 2000">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-300"
                        placeholder="Dietary restrictions, goals...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div id="mealsContainer"></div>

            <button type="button" onclick="addMeal()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition min-h-[44px]">
                <x-icon name="plus" class="w-4 h-4" />
                Add Meal
            </button>

            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-8 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition min-h-[44px]">
                    <x-icon name="check" class="w-4 h-4" />
                    Create Plan & Notify Member
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            const mealTypes = @json($mealTypes);
            let mealCount = 0;

            function addMeal() {
                const mi = mealCount;
                const c = document.getElementById('mealsContainer');
                let opts = '<option value="">Select Meal Type</option>';
                mealTypes.forEach(mt => {
                    opts += '<option value="' + mt.id + '">' + mt.name + '</option>';
                });
                c.insertAdjacentHTML('beforeend', `
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4" id="meal-${mi}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 flex-wrap">
                            <select name="meals[${mi}][meal_type_id]" required class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm min-h-[44px]">${opts}</select>
                            <input type="text" name="meals[${mi}][time]" placeholder="e.g. 8:00 AM" class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm min-h-[44px] w-28">
                        </div>
                        <button type="button" onclick="document.getElementById('meal-${mi}').remove()" class="p-2 rounded-lg hover:bg-red-50 text-red-500 min-h-[44px] min-w-[44px] flex items-center justify-center">✕</button>
                    </div>
                    <div id="items-${mi}" class="space-y-2"></div>
                    <button type="button" onclick="addItem(${mi})" class="mt-3 inline-flex items-center gap-1 text-sm text-indigo-600 font-medium min-h-[44px]">+ Add Food Item</button>
                </div>
            `);
                addItem(mi);
                mealCount++;
            }

            function addItem(mi) {
                const c = document.getElementById('items-' + mi);
                const ii = c.children.length;
                c.insertAdjacentHTML('beforeend', `
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-2 p-3 bg-gray-50 rounded-xl items-end" id="m${mi}i${ii}">
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">Food Name</label>
                        <input type="text" name="meals[${mi}][items][${ii}][food_name]" required class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm min-h-[44px]" placeholder="e.g. Brown Rice">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Qty</label>
                        <input type="text" name="meals[${mi}][items][${ii}][quantity]" required class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm min-h-[44px]" placeholder="1 cup">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Cal</label>
                        <input type="number" name="meals[${mi}][items][${ii}][calories]" min="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm min-h-[44px]">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Protein</label>
                        <input type="number" name="meals[${mi}][items][${ii}][protein]" min="0" step="0.1" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm min-h-[44px]">
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="document.getElementById('m${mi}i${ii}').remove()" class="p-2 rounded-lg hover:bg-red-50 text-red-500 min-h-[44px] min-w-[44px] flex items-center justify-center">✕</button>
                    </div>
                </div>
            `);
            }

            addMeal();
        </script>
    @endpush
</x-trainer-layout>
