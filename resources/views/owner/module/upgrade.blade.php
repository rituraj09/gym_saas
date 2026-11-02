<x-owner-layout>
    <x-slot name="title">Upgrade Required</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mb-4">
                    <x-icon name="sparkles" class="w-10 h-10 text-white" />
                </div>
                <h1 class="text-2xl font-bold text-white">Premium Feature</h1>
                <p class="mt-2 text-indigo-100">
                    The <strong class="text-white">{{ $moduleLabel }}</strong> module is not available on your current
                    plan.
                </p>
            </div>

            <div class="px-6 py-6 space-y-6">
                {{-- Current Plan --}}
                @if ($currentPlan)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Your Current Plan</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-lg font-bold text-gray-900">{{ $currentPlan->name }}</span>
                                <span class="ml-2 text-sm text-gray-500">
                                    ${{ number_format($currentPlan->monthly_price, 2) }}/mo
                                </span>
                            </div>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Current
                            </span>
                        </div>
                        <div class="mt-3">
                            <p class="text-xs text-gray-500 mb-1">Included Modules:</p>
                            <div class="flex flex-wrap gap-2">
                                @forelse($currentPlan->features_list as $feature)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <x-icon name="check-circle" class="w-3 h-3 mr-1" />
                                        {{ $feature }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400">No premium modules included</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800">No active subscription found. Please contact support to set
                            up your plan.</p>
                    </div>
                @endif

                {{-- Unavailable Modules --}}
                @if (count($unavailableModules) > 0)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Modules Not Available on Your Plan:</h3>
                        <div class="space-y-2">
                            @foreach ($unavailableModules as $mod)
                                <div
                                    class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                                    <span class="text-sm font-medium text-red-800">{{ $mod['label'] }}</span>
                                    <x-icon name="x-mark" class="w-5 h-5 text-red-400" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CTA --}}
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 text-center">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-2">Unlock {{ $moduleLabel }}</h3>
                    <p class="text-sm text-indigo-600 mb-4">
                        Upgrade your subscription plan to access this premium module and enhance your gym management
                        capabilities.
                    </p>
                    <a href="mailto:{{ config('mail.from.address', 'support@gym-saas.com') }}?subject=Plan%20Upgrade%20Request%20-%20{{ urlencode($gym->name) }}"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                        <x-icon name="sparkles" class="w-5 h-5 mr-2" />
                        Request Plan Upgrade
                    </a>
                </div>

                {{-- Back link --}}
                <div class="text-center">
                    <a href="{{ route('owner.dashboard') }}"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 min-h-[44px]">
                        <x-icon name="arrow-left" class="w-4 h-4 mr-1" />
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-owner-layout>
