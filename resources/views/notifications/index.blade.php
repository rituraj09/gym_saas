@php
    $layout = auth()->user()->hasRole('platform_admin') ? 'admin-layout' : 'owner-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="title">Notifications</x-slot>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $unreadCount }} unread notification(s)
            </p>
        </div>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                    <x-icon name="check-circle" class="w-4 h-4" />
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('notifications.index') }}" class="flex flex-wrap items-end gap-3">

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="filter" class="rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
                    <option value="">All</option>
                    <option value="unread" {{ request('filter') === 'unread' ? 'selected' : '' }}>
                        Unread Only
                    </option>
                    <option value="read" {{ request('filter') === 'read' ? 'selected' : '' }}>
                        Read Only
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                <select name="type" class="rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
                    <option value="">All Types</option>

                    @foreach (\App\Models\AppNotification::TYPE_LABELS as $code => $label)
                        <option value="{{ $code }}" {{ request('type') === $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                    Filter
                </button>

                <a href="{{ route('notifications.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition min-h-[44px]">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Notification List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        @forelse ($notifications as $notification)
            <div
                class="px-4 sm:px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition
                {{ $notification->is_read ? '' : 'bg-indigo-50/30' }}">

                <div class="flex items-start gap-3 sm:gap-4">

                    {{-- Icon --}}
                    <div
                        class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $notification->type_color }}">
                        <x-icon name="{{ $notification->type_icon }}" class="w-5 h-5" />
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">

                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p
                                    class="text-sm font-semibold {{ $notification->is_read ? 'text-gray-700' : 'text-gray-900' }}">
                                    {{ $notification->title }}

                                    @if (!$notification->is_read)
                                        <span
                                            class="inline-block w-2 h-2 bg-indigo-500 rounded-full ml-1 align-middle"></span>
                                    @endif
                                </p>

                                <p class="text-sm text-gray-500 mt-0.5">
                                    {{ $notification->message }}
                                </p>
                            </div>

                            <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center gap-2 mt-2">

                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $notification->type_color }}">
                                {{ $notification->type_label }}
                            </span>

                            @if (!$notification->is_read)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium min-h-[32px] px-2">
                                        Mark as Read
                                    </button>
                                </form>
                            @endif

                            @if ($notification->link)
                                <a href="{{ $notification->link }}"
                                    class="text-xs text-gray-500 hover:text-gray-700 font-medium min-h-[32px] px-2">
                                    View →
                                </a>
                            @endif

                            <form method="POST" action="{{ route('notifications.destroy', $notification) }}"
                                onsubmit="return confirm('Delete this notification?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="text-xs text-red-400 hover:text-red-600 font-medium min-h-[32px] px-2">
                                    Delete
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="p-12 text-center">
                <x-icon name="bell" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-500 font-medium">No notifications</p>
                <p class="text-gray-400 text-sm mt-1">You're all caught up!</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if ($notifications->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>

</x-dynamic-component>
