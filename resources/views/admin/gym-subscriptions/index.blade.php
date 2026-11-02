<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gym Subscriptions</h1>
            <p class="mt-1 text-sm text-gray-600">View and manage all gym subscriptions.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.saas-plans.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">
                <x-icon name="credit-card" class="w-4 h-4 mr-2" /> SaaS Plans
            </a>
            <a href="{{ route('admin.gym-subscriptions.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" /> Assign Subscription
            </a>
        </div>
    </div>

    @if ($subscriptions->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gym</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($subscriptions as $sub)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">
                                        <a href="{{ route('admin.gym-subscriptions.show', $sub->id) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            {{ $sub->gym->name }}
                                        </a>
                                    </span>
                                    <span class="text-xs text-gray-400 block">{{ $sub->gym->code }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $sub->saasPlan->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sub->status_badge }}">
                                        {{ $sub->status->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sub->start_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sub->end_date ? $sub->end_date->format('M d, Y') : 'Ongoing' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.gym-module-access.index', $sub->gym_id) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-purple-600 hover:bg-purple-50 rounded-lg text-sm font-medium min-h-[44px]">
                                        <x-icon name="cog" class="w-4 h-4" />
                                        <span class="hidden lg:inline">Modules</span>
                                    </a>
                                    @if ($sub->isActive())
                                        <form method="POST"
                                            action="{{ route('admin.gym-subscriptions.cancel', $sub) }}"
                                            onsubmit="return confirm('Cancel this subscription?')">
                                            @csrf
                                            <button type="submit"
                                                class="text-red-600 hover:underline text-sm min-h-[44px]">Cancel</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $subscriptions->links() }}</div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <x-icon name="credit-card" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900">No subscriptions yet</h3>
            <a href="{{ route('admin.gym-subscriptions.create') }}"
                class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" /> Assign First Subscription
            </a>
        </div>
    @endif
</x-admin-layout>
