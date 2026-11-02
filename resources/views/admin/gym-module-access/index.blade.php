<x-admin-layout>
    <x-slot name="title">Module Access — {{ $gym->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.gym-subscriptions.index') }}"
            class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-2">
            <x-icon name="arrow-left" class="w-4 h-4" /> Back to Subscriptions
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Module Access</h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage feature overrides for <strong>{{ $gym->name }}</strong>.
            Overrides take priority over the SaaS plan defaults.
        </p>
    </div>

    {{-- Current Plan Info --}}
    @php
        $plan = $gym->activeSubscription?->saasPlan;
    @endphp
    <div class="bg-indigo-50 rounded-xl border border-indigo-200 p-4 mb-6">
        <div class="flex items-center gap-3">
            <x-icon name="sparkles" class="w-5 h-5 text-indigo-600" />
            <div>
                <p class="text-sm font-semibold text-indigo-900">
                    Current Plan: {{ $plan?->name ?? 'No Active Plan' }}
                </p>
                <p class="text-xs text-indigo-600 mt-0.5">
                    Modules shown as "Plan Default" inherit from this plan. Overrides are marked explicitly.
                </p>
            </div>
        </div>
    </div>

    {{-- Module Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @foreach ($modules as $code => $data)
            @php
                $isOverride = $data['source'] === 'override';
                $isEnabled = $data['enabled'];
                $borderColor = $isEnabled
                    ? ($isOverride
                        ? 'border-green-300 bg-green-50'
                        : 'border-gray-200 bg-white')
                    : ($isOverride
                        ? 'border-red-300 bg-red-50'
                        : 'border-gray-200 bg-white');
            @endphp
            <div class="rounded-xl border-2 {{ $borderColor }} p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $data['label'] }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if ($isOverride)
                                <span class="font-semibold {{ $isEnabled ? 'text-green-700' : 'text-red-700' }}">
                                    Override:
                                </span>
                                {{ $isEnabled ? 'Force Enabled' : 'Force Disabled' }}
                                @if ($data['override']?->expires_at)
                                    · Expires {{ $data['override']->expires_at->format('M d, Y') }}
                                @endif
                            @else
                                Plan Default: {{ $isEnabled ? 'Enabled' : 'Disabled' }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-1">
                        @if ($isEnabled)
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        @else
                            <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        @endif
                    </div>
                </div>

                @if ($isOverride && $data['override'])
                    @php $ov = $data['override']; @endphp
                    <div class="text-xs text-gray-500 mb-3 space-y-1">
                        @if ($ov->reason)
                            <p><strong>Reason:</strong> {{ $ov->reason }}</p>
                        @endif
                        <p><strong>Set by:</strong> {{ $ov->grantedByUser?->name ?? 'Unknown' }}</p>
                        <p><strong>On:</strong> {{ $ov->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="flex items-center gap-2 pt-3 border-t border-gray-200">
                        {{-- Toggle --}}
                        <form method="POST" action="{{ route('admin.gym-module-access.toggle', $ov) }}">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg min-h-[36px] transition
                                        {{ $isEnabled
                                            ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                                            : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                {{ $isEnabled ? 'Disable' : 'Enable' }}
                            </button>
                        </form>

                        {{-- Delete Override --}}
                        <form method="POST" action="{{ route('admin.gym-module-access.destroy', $ov) }}"
                            onsubmit="return confirm('Remove this override? Module will revert to plan default.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 min-h-[36px] transition">
                                Remove Override
                            </button>
                        </form>
                    </div>
                @else
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400 mb-2">
                            Plan default: {{ $data['plan_default'] ? 'Enabled ✓' : 'Disabled ✗' }}
                        </p>
                        <a href="{{ route('admin.gym-module-access.create', $gym) }}?module={{ $code }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 min-h-[36px] transition">
                            <x-icon name="pencil" class="w-3 h-3" />
                            Create Override
                        </a>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Add New Override Button --}}
    @php
        $hasAvailable = collect($modules)->contains(fn($d) => $d['source'] !== 'override');
    @endphp
    @if ($hasAvailable)
        <a href="{{ route('admin.gym-module-access.create', $gym) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
            <x-icon name="plus-circle" class="w-4 h-4" />
            Add Module Override
        </a>
    @endif
</x-admin-layout>
