<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50">

    @php
        $user = auth()->user();
        $currentRoute = request()->route()->getName();
    @endphp

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/50 hidden lg:hidden" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 border-r border-slate-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out flex flex-col">

        <!-- Sidebar Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center">
                    <x-icon name="shield-check" class="w-5 h-5 text-blue-500" />
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-blue-400 truncate">{{ config('app.name') }}</p>
                    <p class="text-xs text-slate-400 truncate">Platform Admin</p>
                </div>
            </a>
            <button onclick="closeSidebar()" class="lg:hidden p-1.5 rounded-lg hover:bg-slate-800">
                <x-icon name="x-mark" class="w-5 h-5 text-slate-400" />
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Main</p>

            <x-admin-sidebar-link href="{{ route('admin.dashboard') }}" icon="home" label="Dashboard"
                :active="str_contains($currentRoute, 'admin.dashboard')" />

            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Subscriptions</p>

            <x-admin-sidebar-link href="{{ route('admin.saas-plans.index') }}" icon="sparkles" label="SaaS Plans"
                :active="request()->routeIs('admin.saas-plans.*')" />
            <x-admin-sidebar-link href="{{ route('admin.gym-subscriptions.index') }}" icon="credit-card"
                label="Gym Subscriptions" :active="request()->routeIs('admin.gym-subscriptions.*')" />

            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Platform</p>

            <x-admin-sidebar-link href="#" icon="building-office" label="All Gyms" :active="false" />
            <x-admin-sidebar-link href="#" icon="users" label="All Users" :active="false" />

            <p class="px-3 mt-5 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">More</p>

            <x-admin-sidebar-link href="#" icon="chart-bar" label="Reports" :active="false" />
            <x-admin-sidebar-link href="#" icon="cog" label="Settings" :active="false" />
        </nav>

        <!-- Sidebar Footer -->
        <div class="border-t border-slate-800 p-3 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-red-500/10 hover:text-red-400 transition w-full min-h-[44px]">
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
                <h1 class="text-lg font-semibold text-gray-900">{{ $headerTitle ?? 'Admin' }}</h1>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Notification Bell (placeholder) -->
                <button class="relative p-2 rounded-xl hover:bg-gray-100 transition">
                    <x-icon name="bell" class="w-5 h-5 text-gray-500" />
                </button>

                <!-- User Info -->
                <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <span
                            class="text-indigo-700 font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-gray-700 max-w-[120px] truncate leading-tight">
                            {{ $user->name }}</p>
                        <p class="text-xs text-gray-500 leading-tight">Admin</p>
                    </div>
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
