<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription Expired — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <x-icon name="shield-check" class="w-8 h-8 text-white" />
            </div>
            <h1 class="text-xl font-bold text-gray-900">{{ config('app.name') }}</h1>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 bg-red-50 border-b border-red-100 text-center">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <x-icon name="exclamation" class="w-7 h-7 text-red-600" />
                </div>
                <h2 class="text-lg font-bold text-red-800">Subscription Inactive</h2>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                <p class="text-sm text-gray-600 text-center">
                    Your gym's SaaS subscription is currently inactive. Access to the dashboard and all features has
                    been restricted.
                </p>

                @if ($subscription)
                    <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Gym</span>
                            <span class="font-medium text-gray-900">{{ $gym->name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Last Plan</span>
                            <span class="font-medium text-gray-900">{{ $subscription->saasPlan?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Status</span>
                            @php
                                $sCode = $subscription->status?->code ?? 'unknown';
                                $sColors = [
                                    'expired' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                    'suspended' => 'bg-yellow-100 text-yellow-800',
                                ];
                                $sClass = $sColors[$sCode] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sClass }}">
                                {{ ucfirst($sCode) }}
                            </span>
                        </div>
                        @if ($subscription->end_date)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Expired On</span>
                                <span
                                    class="font-medium text-gray-900">{{ $subscription->end_date->format('M d, Y') }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-500">No subscription record found for your gym.</p>
                    </div>
                @endif

                <div class="bg-indigo-50 rounded-xl p-4">
                    <p class="text-sm text-indigo-800 font-medium mb-1">Need to renew?</p>
                    <p class="text-xs text-indigo-600">
                        Please contact the platform administrator to renew your subscription and restore full access to
                        all features.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition min-h-[44px]">
                        <x-icon name="logout" class="w-4 h-4" />
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>

</html>
