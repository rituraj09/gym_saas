<x-owner-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="headerTitle">Dashboard</x-slot>

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-2xl p-5 sm:p-6 mb-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="mt-1 text-indigo-100 text-sm sm:text-base">Here's what's happening at {{ $gym->name }}
                    today.</p>
            </div>
            <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2 text-sm">
                <x-icon name="calendar" class="w-4 h-4" />
                <span>{{ now()->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Subscription Alert (trial) -->
    @if ($gym->status?->code === 'trial')
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <x-icon name="sparkles" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
            <div class="text-sm">
                <p class="font-semibold text-amber-900">You're on a Free Trial</p>
                <p class="text-amber-700 mt-0.5">Upgrade to a paid plan to unlock all features and continue using
                    GymManager without interruption.</p>
            </div>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Members -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <x-icon name="users" class="w-5 h-5 text-blue-600" />
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['total_members'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Total Members</p>
        </div>

        <!-- Total Trainers -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <x-icon name="user" class="w-5 h-5 text-green-600" />
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['total_trainers'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Total Trainers</p>
        </div>

        <!-- Active Memberships -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <x-icon name="credit-card" class="w-5 h-5 text-indigo-600" />
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['active_members'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Active Memberships</p>
        </div>

        <!-- Expiring Soon -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <x-icon name="exclamation" class="w-5 h-5 text-amber-600" />
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['expiring_memberships'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Expiring Soon</p>
        </div>
    </div>

    <!-- Two Column: Quick Actions + Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 sm:p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition group">
                    <div
                        class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition">
                        <x-icon name="user" class="w-4 h-4 text-blue-600" />
                    </div>
                    <span class="text-sm font-medium text-gray-700">Add New Member</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition group">
                    <div
                        class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition">
                        <x-icon name="user" class="w-4 h-4 text-green-600" />
                    </div>
                    <span class="text-sm font-medium text-gray-700">Add New Trainer</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition group">
                    <div
                        class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition">
                        <x-icon name="currency" class="w-4 h-4 text-indigo-600" />
                    </div>
                    <span class="text-sm font-medium text-gray-700">Record Payment</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition group">
                    <div
                        class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition">
                        <x-icon name="clipboard" class="w-4 h-4 text-purple-600" />
                    </div>
                    <span class="text-sm font-medium text-gray-700">Create Exercise Plan</span>
                </a>
            </div>
        </div>

        <!-- Recent Activity / Empty State -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5 sm:p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Activity</h3>

            @if ($stats['total_members'] === 0 && $stats['total_trainers'] === 0)
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                        <x-icon name="sparkles" class="w-8 h-8 text-gray-400" />
                    </div>
                    <h4 class="text-base font-semibold text-gray-900 mb-1">Your gym is ready!</h4>
                    <p class="text-sm text-gray-500 max-w-sm">Start by adding your first trainer or member. Activity
                        will appear here once you do.</p>
                    <div class="flex gap-3 mt-5">
                        <a href="#"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            <x-icon name="user" class="w-4 h-4" />
                            Add Member
                        </a>
                        <a href="#"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            <x-icon name="user" class="w-4 h-4" />
                            Add Trainer
                        </a>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500">Activity feed will appear here.</p>
            @endif
        </div>
    </div>
</x-owner-layout>
