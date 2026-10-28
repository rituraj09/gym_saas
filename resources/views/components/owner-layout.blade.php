<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50">

    @php
        $user = auth()->user();
        $gym = $user->gymOwner?->gym;
        $currentRoute = request()->route()->getName();
    @endphp

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/50 hidden lg:hidden" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out flex flex-col">

        <!-- Sidebar Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-gray-100 flex-shrink-0">
            <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-2.5">
                @if ($gym?->logo)
                    <img src="{{ asset('storage/' . $gym->logo) }}" alt="{{ $gym->name }}"
                        class="w-8 h-8 rounded-lg object-cover">
                @else
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                        <span
                            class="text-white font-bold text-sm">{{ strtoupper(substr($gym?->name ?? 'G', 0, 1)) }}</span>
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $gym?->name ?? 'My Gym' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $gym?->city ?? '' }}</p>
                </div>
            </a>
            <button onclick="closeSidebar()" class="lg:hidden p-1.5 rounded-lg hover:bg-gray-100">
                <x-icon name="x-mark" class="w-5 h-5 text-gray-500" />
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Main</p>

            <x-sidebar-link href="{{ route('owner.dashboard') }}" icon="home" label="Dashboard" :active="str_contains($currentRoute, 'owner.dashboard')" />
            <x-sidebar-link href="{{ route('owner.gym-profile.edit') }}" icon="building-office" label="Gym Profile"
                :active="str_contains($currentRoute, 'gym-profile')" />

            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">People</p>

            <x-sidebar-link href="{{ route('owner.trainers.index') }}" icon="users" label="Trainers"
                :active="str_contains($currentRoute, 'trainers')" />
            <x-sidebar-link href="{{ route('owner.members.index') }}" icon="user" label="Members"
                :active="str_contains($currentRoute, 'members')" />

            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>

            <x-sidebar-link href="{{ route('owner.membership-plans.index') }}" icon="clipboard"
                label="Membership Plans" :active="str_contains($currentRoute, 'membership-plans') ||
                    str_contains($currentRoute, 'memberships')" />
            <x-sidebar-link href="{{ route('owner.payments.index') }}" icon="credit-card" label="Payments"
                :active="str_contains($currentRoute, 'payments')" />

            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Plans</p>

            <x-sidebar-link href="{{ route('owner.member-exercise-plans.index') }}" icon="fire" :active="request()->routeIs('owner.member-exercise-plans.*')"
                label="Member Plans" />
            <x-sidebar-link href="{{ route('owner.exercise-templates.index') }}" icon="bolt" :active="request()->routeIs('owner.exercise-templates.*') || request()->routeIs('owner.exercises.*')"
                label="Exercise Library" />
            <x-sidebar-link href="{{ route('owner.member-diet-plans.index') }}" icon="heart" :active="request()->routeIs('owner.member-diet-plans.*') ||
                request()->routeIs('owner.diet-plan-templates.*')"
                label="Diet Plans" />

            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">More</p>

            <x-sidebar-link href="#" icon="chart-bar" label="Reports" :active="false" />
            <x-sidebar-link href="{{ route('owner.my-profile.edit') }}" icon="cog" label="My Profile"
                :active="str_contains($currentRoute, 'my-profile')" />
        </nav>

        <!-- Sidebar Footer -->
        <div class="border-t border-gray-100 p-3 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 transition w-full min-h-[44px]">
                    <x-icon name="logout" class="w-5 h-5" />
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:ml-72 min-h-screen flex flex-col">

        <!-- Top Bar -->
        <header
            class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <button onclick="openSidebar()" class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-gray-100">
                    <x-icon name="bars" class="w-6 h-6 text-gray-700" />
                </button>
                <h1 class="text-lg font-semibold text-gray-900">{{ $headerTitle ?? 'Dashboard' }}</h1>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Notification Bell (placeholder) -->
                <button class="relative p-2 rounded-xl hover:bg-gray-100 transition">
                    <x-icon name="bell" class="w-5 h-5 text-gray-500" />
                </button>

                <!-- User Dropdown -->
                <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <span
                            class="text-indigo-700 font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <span
                        class="hidden sm:block text-sm font-medium text-gray-700 max-w-[120px] truncate">{{ $user->name }}</span>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if (session('success'))
            <div
                class="mx-4 sm:mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="mx-4 sm:mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <x-icon name="exclamation" class="w-5 h-5 flex-shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div
                class="mx-4 sm:mx-6 mt-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <x-icon name="exclamation" class="w-5 h-5 flex-shrink-0" />
                {{ session('warning') }}
            </div>
        @endif

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6">
            {{ $slot }}
        </main>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    </script>

    @livewireScripts
    @stack('scripts')
</body>

</html>
