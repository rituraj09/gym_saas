<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $headerTitle ?? 'Trainer Panel' }} | {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-slate-50 min-h-screen antialiased">

    @php
        $currentRoute = request()->route()?->getName() ?? '';
        $trainer = Auth::user()->trainer;
        $gymName = $trainer?->gym?->name ?? 'Gym';
        $userName = Auth::user()->name;
        $unread = Auth::user()->unreadNotificationCount();

        $isActive = fn($prefix) => str_starts_with($currentRoute, $prefix);
    @endphp

    {{-- Mobile sidebar overlay --}}
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-40 hidden lg:hidden"
        onclick="closeSidebar()">
    </div>

    {{-- Sidebar --}}
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-white border-r border-slate-200
                  -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

        {{-- Brand --}}
        <div class="h-20 px-6 flex items-center gap-3 border-b border-slate-100 shrink-0">

            <div
                class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                <x-icon name="academic-cap" class="w-6 h-6 text-white" />
            </div>

            <div class="min-w-0 flex-1">
                <h1 class="font-bold text-slate-900 text-base">Trainer Panel</h1>
                <p class="text-xs text-slate-500 truncate mt-0.5">{{ $gymName }}</p>
            </div>

            <button onclick="closeSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-7">

            {{-- Main --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-bold tracking-[0.16em] uppercase text-slate-400">
                    Main Menu
                </p>

                <div class="space-y-1">
                    <x-sidebar-link href="{{ route('trainer.dashboard') }}" icon="squares-2x2" label="Dashboard"
                        :active="$currentRoute === 'trainer.dashboard'" />
                </div>
            </div>

            {{-- People --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-bold tracking-[0.16em] uppercase text-slate-400">
                    Members
                </p>

                <div class="space-y-1">
                    <x-sidebar-link href="{{ route('trainer.members.index') }}" icon="user-group" label="All Members"
                        :active="$isActive('trainer.members')" />
                </div>
            </div>

            {{-- Plans --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-bold tracking-[0.16em] uppercase text-slate-400">
                    Training & Nutrition
                </p>

                <div class="space-y-1">
                    <x-sidebar-link href="{{ route('trainer.exercise-plans.index') }}" icon="clipboard-document-list"
                        label="Exercise Plans" :active="$isActive('trainer.exercise-plans')" />

                    <x-sidebar-link href="{{ route('trainer.diet-plans.index') }}" icon="clipboard-document-check"
                        label="Diet Plans" :active="$isActive('trainer.diet-plans')" />
                </div>
            </div>

            {{-- Account --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-bold tracking-[0.16em] uppercase text-slate-400">
                    Account
                </p>

                <div class="space-y-1">
                    <x-sidebar-link href="{{ route('trainer.notifications.index') }}" icon="bell"
                        label="Notifications" :active="$isActive('trainer.notifications')" />

                    <x-sidebar-link href="{{ route('trainer.profile.edit') }}" icon="user-circle" label="My Profile"
                        :active="$isActive('trainer.profile')" />
                </div>
            </div>

        </nav>

        {{-- Sidebar bottom --}}
        <div class="p-4 border-t border-slate-100 shrink-0">

            <div class="flex items-center gap-3 p-3 mb-3 rounded-xl bg-slate-50">
                <div
                    class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shrink-0">
                    {{ strtoupper(substr($userName, 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-800 truncate">
                        {{ $userName }}
                    </p>
                    <p class="text-xs text-slate-500">Trainer</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    class="flex items-center gap-3 w-full px-3 py-3 rounded-xl
                               text-sm font-medium text-slate-600
                               hover:bg-red-50 hover:text-red-600 transition-colors">

                    <x-icon name="arrow-right-start-on-rectangle" class="w-5 h-5" />
                    Sign Out
                </button>
            </form>

        </div>
    </aside>

    {{-- Main application --}}
    <div class="lg:ml-72 min-h-screen flex flex-col">

        {{-- Header --}}
        <header
            class="sticky top-0 z-30 h-16 sm:h-20 bg-white/90 backdrop-blur-xl
                       border-b border-slate-200 px-4 sm:px-6 lg:px-8
                       flex items-center gap-3">

            {{-- Mobile menu --}}
            <button onclick="openSidebar()" aria-label="Open navigation"
                class="lg:hidden flex items-center justify-center w-10 h-10
                           rounded-xl text-slate-600 hover:bg-slate-100 shrink-0">

                <x-icon name="bars-3" class="w-6 h-6" />
            </button>

            {{-- Page title --}}
            <div class="min-w-0">
                <h2 class="text-base sm:text-xl font-bold text-slate-900 truncate">
                    {{ $headerTitle ?? 'Trainer Panel' }}
                </h2>
                <p class="hidden sm:block text-xs text-slate-400 mt-0.5">
                    Manage your members and training activities
                </p>
            </div>

            <div class="ml-auto flex items-center gap-2 sm:gap-4">

                {{-- Notifications --}}
                <a href="{{ route('trainer.notifications.index') }}" aria-label="Notifications"
                    class="relative flex items-center justify-center w-10 h-10
                          rounded-xl text-slate-500 hover:bg-slate-100 transition">

                    <x-icon name="bell" class="w-5 h-5" />

                    @if ($unread > 0)
                        <span
                            class="absolute top-1 right-1 min-w-[17px] h-[17px]
                                     px-1 rounded-full bg-red-500 text-white
                                     text-[10px] font-bold flex items-center justify-center
                                     ring-2 ring-white">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </a>

                <div class="hidden sm:block h-8 w-px bg-slate-200"></div>

                {{-- User information --}}
                <div class="hidden sm:flex items-center gap-2.5">

                    <div
                        class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700
                                flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($userName, 0, 1)) }}
                    </div>

                    <div class="max-w-36">
                        <p class="text-sm font-semibold text-slate-800 truncate">
                            {{ $userName }}
                        </p>
                        <p class="text-xs text-slate-400">Trainer Account</p>
                    </div>

                </div>

            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 w-full max-w-[1600px] mx-auto p-4 sm:p-6 lg:p-8">

            {{-- Success message --}}
            @if (session('success'))
                <div
                    class="mb-6 flex items-start gap-3 p-4 rounded-2xl
                            border border-emerald-200 bg-emerald-50 text-emerald-800">

                    <x-icon name="check-circle" class="w-5 h-5 shrink-0 mt-0.5" />

                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Error message --}}
            @if (session('error'))
                <div
                    class="mb-6 flex items-start gap-3 p-4 rounded-2xl
                            border border-red-200 bg-red-50 text-red-800">

                    <x-icon name="exclamation-circle" class="w-5 h-5 shrink-0 mt-0.5" />

                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl border border-red-200 bg-red-50 text-red-800">
                    <div class="flex gap-2 items-center font-semibold text-sm mb-2">
                        <x-icon name="exclamation-triangle" class="w-5 h-5" />
                        Please correct the following errors.
                    </div>

                    <ul class="list-disc pl-8 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}

        </main>

        <footer class="px-6 py-4 border-t border-slate-200 bg-white">
            <p class="text-xs text-center text-slate-400">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </footer>

    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') closeSidebar();
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) closeSidebar();
        });
    </script>

    @livewireScripts
    @stack('scripts')

</body>

</html>
