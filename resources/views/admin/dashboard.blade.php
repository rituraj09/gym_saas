<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Platform Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Overview of your SaaS platform</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total Gyms --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Gyms</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ \App\Models\Gym::count() }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <x-icon name="building-office" class="w-5 h-5 text-indigo-600" />
                </div>
            </div>
        </div>

        {{-- Active Subscriptions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active Subscriptions</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">
                        {{ \App\Models\GymSubscription::whereHas('status', fn($q) => $q->where('code', 'active'))->count() }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <x-icon name="credit-card" class="w-5 h-5 text-green-600" />
                </div>
            </div>
        </div>

        {{-- SaaS Plans --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">SaaS Plans</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ \App\Models\SaasPlan::count() }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <x-icon name="sparkles" class="w-5 h-5 text-purple-600" />
                </div>
            </div>
        </div>

        {{-- Expired Subscriptions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Expired / Suspended</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">
                        {{ \App\Models\GymSubscription::whereHas('status', fn($q) => $q->whereIn('code', ['expired', 'suspended']))->count() }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <x-icon name="exclamation" class="w-5 h-5 text-red-600" />
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('admin.saas-plans.index') }}"
            class="block bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-md transition">
            <div class="flex items-center gap-3">
                <x-icon name="sparkles" class="w-6 h-6 text-indigo-600" />
                <div>
                    <p class="font-semibold text-gray-900">Manage SaaS Plans</p>
                    <p class="text-sm text-gray-500">Create, edit, and configure subscription tiers</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.gym-subscriptions.index') }}"
            class="block bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-md transition">
            <div class="flex items-center gap-3">
                <x-icon name="credit-card" class="w-6 h-6 text-indigo-600" />
                <div>
                    <p class="font-semibold text-gray-900">Gym Subscriptions</p>
                    <p class="text-sm text-gray-500">Assign and manage gym subscriptions</p>
                </div>
            </div>
        </a>
    </div>
</x-admin-layout>
