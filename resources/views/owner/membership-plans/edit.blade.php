<x-owner-layout>
    <x-slot name="title">Edit Plan</x-slot>
    <x-slot name="headerTitle">Edit Membership Plan</x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('owner.membership-plans.update', $membershipPlan) }}"
            class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6 space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Plan Name <span
                        class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $membershipPlan->name) }}"
                    required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-1.5">Duration (days)
                        <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_days" id="duration_days"
                        value="{{ old('duration_days', $membershipPlan->duration_days) }}" min="1" max="3650"
                        required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1.5">Price <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="price" id="price"
                        value="{{ old('price', $membershipPlan->price) }}" step="0.01" min="0" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">{{ old('description', $membershipPlan->description) }}</textarea>
            </div>
            <div class="flex flex-col sm:flex-row-reverse gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px]">
                    <x-icon name="check-circle" class="w-5 h-5" /> Save Changes
                </button>
                <a href="{{ route('owner.membership-plans.show', $membershipPlan) }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition min-h-[44px]">Cancel</a>
            </div>
        </form>
    </div>
</x-owner-layout>
