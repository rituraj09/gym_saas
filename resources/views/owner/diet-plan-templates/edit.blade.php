<x-owner-layout>
    <div class="mb-6">
        <a href="{{ route('owner.diet-plan-templates.show', $template) }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Template
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Diet Plan Template</h1>
        <p class="mt-1 text-sm text-gray-600">Update the diet plan details, meals, and food items.</p>
    </div>

    <form method="POST" action="{{ route('owner.diet-plan-templates.update', $template) }}" x-data="dietPlanForm(@js(
    $template->meals
        ->map(
            fn($meal) => [
                'meal_type_id' => $meal->meal_type_id,
                'meal_time' => $meal->meal_time ? \Carbon\Carbon::parse($meal->meal_time)->format('H:i') : '',
                'notes' => $meal->notes ?? '',
                'items' => $meal->items
                    ->map(
                        fn($item) => [
                            'food_name' => $item->food_name,
                            'quantity' => $item->quantity,
                            'calories' => $item->calories,
                            'protein_g' => $item->protein_g,
                            'carbs_g' => $item->carbs_g,
                            'fat_g' => $item->fat_g,
                        ],
                    )
                    ->values()
                    ->toArray(),
            ],
        )
        ->values(),
))"
        class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Template Details --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Plan Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Goal</label>
                    <select name="target_goal"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                        <option value="">-- Select --</option>
                        <option value="Weight Loss" @selected(old('target_goal', $template->target_goal) === 'Weight Loss')>Weight Loss</option>
                        <option value="Muscle Gain" @selected(old('target_goal', $template->target_goal) === 'Muscle Gain')>Muscle Gain</option>
                        <option value="Maintenance" @selected(old('target_goal', $template->target_goal) === 'Maintenance')>Maintenance</option>
                        <option value="Fat Loss" @selected(old('target_goal', $template->target_goal) === 'Fat Loss')>Fat Loss</option>
                        <option value="Endurance" @selected(old('target_goal', $template->target_goal) === 'Endurance')>Endurance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Calories</label>
                    <input type="number" name="total_calories"
                        value="{{ old('total_calories', $template->total_calories) }}" min="0"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Protein (g)</label>
                    <input type="number" name="total_protein_g"
                        value="{{ old('total_protein_g', $template->total_protein_g) }}" min="0" step="0.1"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carbs (g)</label>
                    <input type="number" name="total_carbs_g"
                        value="{{ old('total_carbs_g', $template->total_carbs_g) }}" min="0" step="0.1"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fat (g)</label>
                    <input type="number" name="total_fat_g" value="{{ old('total_fat_g', $template->total_fat_g) }}"
                        min="0" step="0.1"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $template->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Meals --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Meals <span class="text-red-500">*</span></h2>
                    <p class="text-xs text-gray-500 mt-1">Add meals with food items and macro breakdown.</p>
                </div>
                <button type="button" @click="addMeal()"
                    class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    <x-icon name="plus-circle" class="w-4 h-4 mr-1" /> Add Meal
                </button>
            </div>

            @error('meals')
                <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
            @enderror

            <div class="space-y-4">
                <template x-for="(meal, mIndex) in meals" :key="mIndex">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-medium text-gray-900" x-text="'Meal #' + (mIndex + 1)"></span>
                            <button type="button" @click="removeMeal(mIndex)"
                                class="text-red-500 hover:text-red-700 min-h-[44px] min-w-[44px] flex items-center justify-center">
                                <x-icon name="x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Meal Type</label>
                                <select :name="'meals[' + mIndex + '][meal_type_id]'" x-model="meal.meal_type_id"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                                    <option value="">Select...</option>
                                    @foreach ($mealTypes as $mt)
                                        <option value="{{ $mt->id }}">{{ $mt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Time</label>
                                <input type="time" :name="'meals[' + mIndex + '][meal_time]'"
                                    x-model="meal.meal_time"
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                                <input type="text" :name="'meals[' + mIndex + '][notes]'" x-model="meal.notes"
                                    placeholder="e.g., High protein"
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                            </div>
                        </div>

                        <div class="ml-2 border-l-2 border-indigo-200 pl-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-700">Food Items</span>
                                <button type="button" @click="addItem(mIndex)"
                                    class="text-xs text-indigo-600 hover:underline min-h-[32px]">+ Add Food</button>
                            </div>
                            <template x-for="(item, iIndex) in meal.items" :key="iIndex">
                                <div class="grid grid-cols-2 sm:grid-cols-7 gap-2 mb-2 items-end">
                                    <div class="sm:col-span-2">
                                        <input type="text"
                                            :name="'meals[' + mIndex + '][items][' + iIndex + '][food_name]'"
                                            x-model="item.food_name" placeholder="Food name" required
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm min-h-[40px]">
                                    </div>
                                    <div>
                                        <input type="text"
                                            :name="'meals[' + mIndex + '][items][' + iIndex + '][quantity]'"
                                            x-model="item.quantity" placeholder="Qty" required
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm min-h-[40px]">
                                    </div>
                                    <div>
                                        <input type="number"
                                            :name="'meals[' + mIndex + '][items][' + iIndex + '][calories]'"
                                            x-model="item.calories" placeholder="Cal" required min="0"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm min-h-[40px]">
                                    </div>
                                    <div>
                                        <input type="number"
                                            :name="'meals[' + mIndex + '][items][' + iIndex + '][protein_g]'"
                                            x-model="item.protein_g" placeholder="P" required min="0"
                                            step="0.1"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm min-h-[40px]">
                                    </div>
                                    <div>
                                        <input type="number"
                                            :name="'meals[' + mIndex + '][items][' + iIndex + '][carbs_g]'"
                                            x-model="item.carbs_g" placeholder="C" required min="0"
                                            step="0.1"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm min-h-[40px]">
                                    </div>
                                    <div class="flex gap-1">
                                        <input type="number"
                                            :name="'meals[' + mIndex + '][items][' + iIndex + '][fat_g]'"
                                            x-model="item.fat_g" placeholder="F" required min="0"
                                            step="0.1"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm min-h-[40px]">
                                        <button type="button" @click="removeItem(mIndex, iIndex)"
                                            class="text-red-400 hover:text-red-600 min-h-[40px] min-w-[40px] flex items-center justify-center">
                                            <x-icon name="x-mark" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
            <a href="{{ route('owner.diet-plan-templates.show', $template) }}"
                class="w-full sm:w-auto text-center px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">Cancel</a>
            <button type="submit"
                class="w-full sm:w-auto px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="check-circle" class="w-4 h-4 inline mr-1" /> Update Template
            </button>
        </div>
    </form>

    <script>
        function dietPlanForm(existingMeals) {
            return {
                meals: existingMeals.length > 0 ? existingMeals : [],
                addMeal() {
                    this.meals.push({
                        meal_type_id: '',
                        meal_time: '',
                        notes: '',
                        items: [{
                            food_name: '',
                            quantity: '',
                            calories: '',
                            protein_g: '',
                            carbs_g: '',
                            fat_g: ''
                        }]
                    });
                },
                removeMeal(mIndex) {
                    this.meals.splice(mIndex, 1);
                },
                addItem(mIndex) {
                    this.meals[mIndex].items.push({
                        food_name: '',
                        quantity: '',
                        calories: '',
                        protein_g: '',
                        carbs_g: '',
                        fat_g: ''
                    });
                },
                removeItem(mIndex, iIndex) {
                    this.meals[mIndex].items.splice(iIndex, 1);
                }
            };
        }
    </script>
</x-owner-layout>
