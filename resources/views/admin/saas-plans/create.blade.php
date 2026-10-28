<x-admin-layout>
    <div class="mb-6">
        <a href="{{ route('admin.saas-plans.index') }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Plans
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create SaaS Plan</h1>
        <p class="mt-1 text-sm text-gray-600">Define a new subscription tier with pricing and feature limits.</p>
    </div>

    <form method="POST" action="{{ route('admin.saas-plans.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Info</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g., Basic, Pro"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required
                        placeholder="e.g., basic, pro"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Price ($) <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="monthly_price" value="{{ old('monthly_price', '0.00') }}" min="0"
                        step="0.01" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Yearly Price ($) <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="yearly_price" value="{{ old('yearly_price', '0.00') }}" min="0"
                        step="0.01" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Limits</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Trainers <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="max_trainers" value="{{ old('max_trainers', 0) }}" min="0"
                        required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    <p class="text-xs text-gray-400 mt-1">0 = Unlimited</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Members <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="max_members" value="{{ old('max_members', 0) }}" min="0"
                        required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    <p class="text-xs text-gray-400 mt-1">0 = Unlimited</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Features</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label
                    class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer min-h-[44px]">
                    <input type="hidden" name="has_exercise_module" value="0">
                    <input type="checkbox" name="has_exercise_module" value="1" @checked(old('has_exercise_module'))
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                    <span class="text-sm font-medium text-gray-700">Exercise Plans Module</span>
                </label>
                <label
                    class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer min-h-[44px]">
                    <input type="hidden" name="has_diet_module" value="0">
                    <input type="checkbox" name="has_diet_module" value="1" @checked(old('has_diet_module'))
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                    <span class="text-sm font-medium text-gray-700">Diet Plans Module</span>
                </label>
                <label
                    class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer min-h-[44px]">
                    <input type="hidden" name="has_reports_module" value="0">
                    <input type="checkbox" name="has_reports_module" value="1" @checked(old('has_reports_module'))
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                    <span class="text-sm font-medium text-gray-700">Reports Module</span>
                </label>
                <label
                    class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer min-h-[44px]">
                    <input type="hidden" name="has_priority_support" value="0">
                    <input type="checkbox" name="has_priority_support" value="1" @checked(old('has_priority_support'))
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                    <span class="text-sm font-medium text-gray-700">Priority Support</span>
                </label>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
            <a href="{{ route('admin.saas-plans.index') }}"
                class="w-full sm:w-auto text-center px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">Cancel</a>
            <button type="submit"
                class="w-full sm:w-auto px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="check-circle" class="w-4 h-4 inline mr-1" /> Create Plan
            </button>
        </div>
    </form>
</x-admin-layout>
