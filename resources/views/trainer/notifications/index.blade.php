<x-trainer-layout>
    <x-slot name="headerTitle">Notifications</x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        @if ($notifications->isNotEmpty())
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">Alerts about new members, membership expiries, and more.</p>
                <form method="POST" action="{{ route('trainer.notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition min-h-[44px]">
                        <x-icon name="check" class="w-4 h-4" />
                        Mark All Read
                    </button>
                </form>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse($notifications as $notif)
                    <div class="px-6 py-4 flex items-start gap-4 {{ !$notif->read_at ? 'bg-indigo-50/40' : '' }}">
                        <div class="flex-shrink-0 mt-1">
                            @if (!$notif->read_at)
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 block"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-gray-200 block"></span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            @php
                                $typeColor = match ($notif->type ?? '') {
                                    'new_member' => 'text-blue-600',
                                    'member_membership_expired' => 'text-red-600',
                                    'membership_expiring' => 'text-yellow-600',
                                    default => 'text-gray-600',
                                };
                            @endphp
                            <p class="font-medium text-gray-900">{{ $notif->title }}</p>
                            <p class="text-sm text-gray-600 mt-0.5">{{ $notif->message }}</p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                                @if (!$notif->read_at)
                                    <form method="POST" action="{{ route('trainer.notifications.read', $notif) }}">
                                        @csrf @method('PATCH')
                                        <button
                                            class="text-xs text-indigo-600 font-medium hover:text-indigo-700 min-h-[44px] flex items-center">Mark
                                            as read</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-gray-400">
                        <x-icon name="bell" class="w-10 h-10 mx-auto text-gray-300 mb-3" />
                        <p class="font-medium">No notifications yet</p>
                        <p class="text-sm mt-1">You'll be notified when new members join or memberships expire.</p>
                    </div>
                @endforelse
            </div>
            @if ($notifications->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
</x-trainer-layout>
