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

            <x-sidebar-link href="{{ route('owner.reports.overview') }}" icon="chart-bar" label="Reports"
                :active="request()->routeIs('owner.reports.*')" />
            <x-sidebar-link href="{{ route('owner.my-profile.edit') }}" icon="cog" label="My Profile"
                :active="str_contains($currentRoute, 'my-profile')" />

            <x-sidebar-link href="{{ route('owner.audit-logs.index') }}" icon="document-text" label="Activity Log"
                :active="str_contains($currentRoute, 'audit-logs')" />
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
                <!-- Notification Bell -->
                <div class="relative" x-data="notificationBell()" x-init="init()">
                    <button @click="toggle()"
                        class="relative p-2 rounded-xl hover:bg-gray-100 transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                        <x-icon name="bell" class="w-5 h-5 text-gray-500" />
                        <span x-show="unreadCount > 0" x-cloak
                            class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"
                            x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                    </button>

                    {{-- Dropdown Panel --}}
                    <div x-show="open" @click.away="open = false" x-cloak x-transition
                        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                        {{-- Header --}}
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                            <div class="flex items-center gap-2">
                                <span x-show="unreadCount > 0" x-cloak
                                    class="text-xs text-indigo-600 font-medium cursor-pointer hover:underline"
                                    @click="markAllRead()">
                                    Mark all read
                                </span>
                                <a href="{{ route('notifications.index') }}"
                                    class="text-xs text-gray-500 hover:text-gray-700 font-medium">
                                    View all
                                </a>
                            </div>
                        </div>

                        {{-- Loading --}}
                        <div x-show="loading" class="px-4 py-8 text-center text-gray-400 text-sm">
                            Loading...
                        </div>

                        {{-- Notification List --}}
                        <div x-show="!loading" class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="n.link || '{{ route('notifications.index') }}'" @click.prevent="markRead(n)"
                                    class="block px-4 py-3 hover:bg-gray-50 transition"
                                    :class="n.is_read ? 'bg-white' : 'bg-indigo-50/50'">
                                    <div class="flex items-start gap-3">
                                        <span
                                            class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                                            :class="n.type_color">
                                            <x-icon name="bell" class="w-4 h-4" />
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate" x-text="n.title">
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="n.message">
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1" x-text="n.time_ago"></p>
                                        </div>
                                        <span x-show="!n.is_read"
                                            class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0 mt-2"></span>
                                    </div>
                                </a>
                            </template>

                            <div x-show="notifications.length === 0 && !loading" class="px-4 py-8 text-center">
                                <x-icon name="bell" class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                                <p class="text-sm text-gray-400">No notifications</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ userMenuOpen: false }">

                    <!-- User Info Button -->
                    <button type="button" @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false"
                        class="flex items-center gap-2 pl-3 border-l border-gray-200 hover:bg-gray-50 rounded-lg py-1.5 pr-2 transition">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-indigo-700 font-semibold text-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>

                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-medium text-gray-700 max-w-[120px] truncate leading-tight">
                                {{ $user->name }}
                            </p>
                            <p class="text-xs text-gray-500 leading-tight">Gym Owner</p>
                        </div>

                        <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform"
                            ::class="{ 'rotate-180': userMenuOpen }" />
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenuOpen" x-cloak @click.away="userMenuOpen = false" x-transition
                        class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden">

                        <!-- User Details -->
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <p class="text-sm font-semibold text-gray-800 truncate">
                                {{ $user->name }}
                            </p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">
                                {{ $user->email }}
                            </p>
                            <p class="text-xs text-indigo-600 font-medium mt-1">
                                {{ $gym?->name ?? 'My Gym' }}
                            </p>
                        </div>

                        <!-- Menu Items -->
                        <div class="p-2 space-y-1">

                            <!-- My Profile -->
                            <a href="{{ route('owner.my-profile.edit') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-indigo-600 transition">
                                <x-icon name="cog" class="w-5 h-5 text-gray-400" />
                                <span>My Profile</span>
                            </a>

                            <!-- Gym Profile -->
                            <a href="{{ route('owner.gym-profile.edit') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-indigo-600 transition">
                                <x-icon name="building-office" class="w-5 h-5 text-gray-400" />
                                <span>Gym Profile</span>
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 transition w-full text-left">
                                    <x-icon name="logout" class="w-5 h-5 text-gray-400" />
                                    <span>Log Out</span>
                                </button>
                            </form>

                        </div>
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
        {{-- Expiring Soon Banner --}}
        @php
            $gym = $user->gymOwner?->gym;
            $gymSub = $gym?->activeSubscription;
        @endphp
        @if ($gymSub && $gymSub->isExpiringSoon(7))
            <div
                class="mx-4 sm:mx-6 mt-4 bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <x-icon name="exclamation" class="w-5 h-5 flex-shrink-0 text-yellow-600" />
                <span>
                    <strong>Subscription expiring soon!</strong>
                    Your {{ $gymSub->saasPlan?->name }} plan expires on
                    <strong>{{ $gymSub->end_date->format('M d, Y') }}</strong>
                    ({{ $gymSub->days_remaining }} days remaining).
                    Contact the platform admin to renew.
                </span>
            </div>
        @endif
        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6">
            {{ $slot }}
        </main>
    </div>
    <script>
        function notificationBell() {
            return {
                open: false,
                loading: false,
                notifications: [],
                unreadCount: 0,

                init() {
                    this.fetchNotifications();
                    // Refresh every 60 seconds
                    setInterval(() => this.fetchNotifications(), 60000);
                },

                toggle() {
                    this.open = !this.open;
                    if (this.open) this.fetchNotifications();
                },

                async fetchNotifications() {
                    this.loading = this.notifications.length === 0;
                    try {
                        const res = await fetch('{{ route('notifications.recent') }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                    } catch (e) {
                        console.error('Failed to fetch notifications', e);
                    } finally {
                        this.loading = false;
                    }
                },

                async markRead(n) {
                    if (!n.is_read) {
                        try {
                            await fetch(`/notifications/${n.id}/read`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            n.is_read = true;
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                        } catch (e) {
                            console.error(e);
                        }
                    }
                    if (n.link) window.location.href = n.link;
                },

                async markAllRead() {
                    try {
                        await fetch('{{ route('notifications.mark-all-read') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        this.notifications.forEach(n => n.is_read = true);
                        this.unreadCount = 0;
                    } catch (e) {
                        console.error(e);
                    }
                }
            }
        }
    </script>
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
