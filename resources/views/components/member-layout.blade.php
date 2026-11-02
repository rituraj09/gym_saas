<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Panel — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 min-h-screen">

    @php $currentRoute = request()->route()?->getName() ?? ''; @endphp

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

    <aside id="sidebar"
        class="fixed top-0 left-0 h-full w-72 bg-white border-r border-gray-200 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">

        <div class="h-16 flex items-center gap-3 px-5 border-b border-gray-100">
            <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center">
                <x-icon name="user" class="w-5 h-5 text-white" />
            </div>
            <div>
                <h1 class="text-base font-bold text-gray-900 leading-tight">Member Panel</h1>
                <p class="text-xs text-gray-400">{{ Auth::user()->member?->gym?->name ?? 'Gym' }}</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Main</p>
            <x-sidebar-link href="{{ route('member.dashboard') }}" icon="home" label="Dashboard" :active="str_contains($currentRoute, 'member.dashboard')" />

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-5 mb-2 px-3">My Plans</p>
            <x-sidebar-link href="{{ route('member.exercise-plan.index') }}" icon="clipboard-document-list"
                label="Exercise Plan" :active="str_contains($currentRoute, 'exercise-plan')" />
            <x-sidebar-link href="{{ route('member.diet-plan.index') }}" icon="cake" label="Diet Plan"
                :active="str_contains($currentRoute, 'diet-plan')" />

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-5 mb-2 px-3">My Account</p>
            <x-sidebar-link href="{{ route('member.membership.index') }}" icon="credit-card" label="My Membership"
                :active="str_contains($currentRoute, 'membership')" />
            <x-sidebar-link href="{{ route('member.notifications.index') }}" icon="bell" label="Notifications"
                :active="str_contains($currentRoute, 'notifications')" />
            <x-sidebar-link href="{{ route('member.profile.edit') }}" icon="user-circle" label="My Profile"
                :active="str_contains($currentRoute, 'profile')" />
        </nav>

        <div class="p-4 border-t border-gray-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition w-full min-h-[44px]">
                    <x-icon name="arrow-left-on-rectangle" class="w-5 h-5" />
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="lg:ml-72 min-h-screen flex flex-col">
        <header
            class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-100 h-16 flex items-center px-4 sm:px-6 gap-4">
            <button onclick="openSidebar()"
                class="lg:hidden p-2 -ml-2 rounded-xl hover:bg-gray-100 transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                <x-icon name="bars-3" class="w-6 h-6 text-gray-600" />
            </button>
            <h2 class="text-lg font-bold text-gray-900 truncate">{{ $headerTitle ?? 'Member Panel' }}</h2>
            @php $unread = Auth::user()->unreadNotificationCount(); @endphp
            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('member.notifications.index') }}"
                    class="relative p-2 rounded-xl hover:bg-gray-100 transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <x-icon name="bell" class="w-5 h-5 text-gray-500" />
                    @if ($unread > 0)
                        <span
                            class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                </a>
                <span class="text-sm text-gray-500 hidden sm:block">{{ Auth::user()->name }}</span>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
                    <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div
                    class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm flex items-center gap-2">
                    <x-icon name="exclamation-circle" class="w-5 h-5 text-red-500" />
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.remove('hidden');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    </script>
    @livewireScripts
    @stack('scripts')
</body>

</html>
