<x-owner-layout>
    <x-slot name="title">{{ $membershipPlan->name }}</x-slot>
    <x-slot name="headerTitle">Plan Details</x-slot>

    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $membershipPlan->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $membershipPlan->duration_label }} ·
                        {{ $membershipPlan->memberships_count }} membership(s) assigned</p>
                </div>
                <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $membershipPlan->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                    {{ $membershipPlan->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="text-3xl font-bold text-indigo-600 mb-4">{{ number_format($membershipPlan->price, 2) }}</p>
            @if ($membershipPlan->description)
                <p class="text-sm text-gray-600 mb-4">{{ $membershipPlan->description }}</p>
            @endif
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('owner.membership-plans.edit', $membershipPlan) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition min-h-[44px]">
                    <x-icon name="cog" class="w-4 h-4" /> Edit
                </a>
                <form method="POST" action="{{ route('owner.membership-plans.toggle-status', $membershipPlan) }}"
                    onsubmit="return confirm('{{ $membershipPlan->is_active ? 'Deactivate' : 'Activate' }} this plan?')">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 {{ $membershipPlan->is_active ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200' }} border text-sm font-medium rounded-xl transition min-h-[44px]">
                        {{ $membershipPlan->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
        <a href="{{ route('owner.membership-plans.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-indigo-600 transition">← Back to
            Plans</a>
    </div>
</x-owner-layout>
