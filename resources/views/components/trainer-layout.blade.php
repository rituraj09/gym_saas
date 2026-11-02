<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Panel — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 min-h-screen">

    @php
        $user = auth()->user();
        $trainer = $user->trainer;
        $gym = $trainer?->gym;
        $currentRoute = request()->route()?->getName() ?? '';
    @endphp

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/50 hidden lg:hidden" onclick="closeSidebar()">
    </div>

    <!-- Trainer Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200
           transform -translate-x-full lg:translate-x-0
           transition-transform duration-200 ease-in-out flex flex-col">

        <!-- Sidebar Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-gray-100 flex-shrink-0">

            <a href="{{ route('trainer.dashboard') }}" class="flex items-center gap-2.5 min-w-0">

                @if ($gym?->logo)
                    <img src="{{ asset('storage/' . $gym->logo) }}" alt="{{ $gym->name }}"
                        class="w-9 h-9 rounded-lg object-cover">
                @else
                    <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center">
                        <span class="text-white font-bold text-sm">
                            {{ strtoupper(substr($gym?->name ?? 'G', 0, 1)) }}
                        </span>
                    </div>
                @endif

                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">
                        {{ $gym?->name ?? 'My Gym' }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        Trainer Panel
                    </p>
                </div>
            </a>

            <button type="button" onclick="closeSidebar()" class="lg:hidden p-1.5 rounded-lg hover:bg-gray-100">
                <x-icon name="x-mark" class="w-5 h-5 text-gray-500" />
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

            <!-- Main -->
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Main
            </p>

            <x-sidebar-link href="{{ route('trainer.dashboard') }}" icon="home" label="Dashboard"
                :active="request()->routeIs('trainer.dashboard')" />

            <!-- People -->
            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                People
            </p>

            <x-sidebar-link href="{{ route('trainer.members.index') }}" icon="users" label="Members"
                :active="request()->routeIs('trainer.members.*')" />

            <!-- Training -->
            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Training & Nutrition
            </p>

            <x-sidebar-link href="{{ route('trainer.exercise-plans.index') }}" icon="fire" label="Exercise Plans"
                :active="request()->routeIs('trainer.exercise-plans.*')" />

            <x-sidebar-link href="{{ route('trainer.diet-plans.index') }}" icon="heart" label="Diet Plans"
                :active="request()->routeIs('trainer.diet-plans.*')" />

            <!-- More -->
            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                More
            </p>

            <x-sidebar-link href="{{ route('notifications.index') }}" icon="bell" label="Notifications"
                :active="request()->routeIs('notifications.*')" />

            <x-sidebar-link href="{{ route('trainer.profile.edit') }}" icon="user" label="My Profile"
                :active="request()->routeIs('trainer.profile.*')" />

        </nav>

        <!-- Sidebar Footer -->
        <div class="border-t border-gray-100 p-3 flex-shrink-0">

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                       text-sm font-medium text-gray-600
                       hover:bg-red-50 hover:text-red-600
                       transition w-full min-h-[44px]">

                    <x-icon name="logout" class="w-5 h-5" />
                    <span>Log Out</span>
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
            <h2 class="text-lg font-bold text-gray-900 truncate">{{ $headerTitle ?? 'Trainer Panel' }}</h2>
            @php $unread = Auth::user()->unreadNotificationCount(); @endphp
            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('trainer.notifications.index') }}"
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
