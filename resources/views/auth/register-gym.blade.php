<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register Your Gym — {{ config('app.name', 'GymManager') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 min-h-screen">

    <!-- Top Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-lg sm:text-xl font-bold text-gray-900">{{ config('app.name') }}</span>
            </a>
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600">
                Already registered? <span class="text-indigo-600">Sign in</span>
            </a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <!-- Page Heading -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-100 mb-4">
                <x-icon name="sparkles" class="w-7 h-7 text-indigo-600" />
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">Register Your Gym</h1>
            <p class="mt-3 text-gray-600 max-w-2xl mx-auto text-sm sm:text-base">
                Create your free trial account in less than 2 minutes. No credit card required.
            </p>
        </div>

        <!-- Instructions Card -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 sm:p-5 mb-8">
            <div class="flex gap-3">
                <x-icon name="check-circle" class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" />
                <div class="text-sm text-indigo-900">
                    <p class="font-semibold mb-1">What happens next?</p>
                    <ul class="list-disc list-inside space-y-0.5 text-indigo-800">
                        <li>Your gym will be created with a free trial subscription.</li>
                        <li>You will be automatically logged in to your dashboard.</li>
                        <li>You can start adding trainers, members, and plans right away.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Error Banner -->
        @if ($errors->has('registration'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700">
                {{ $errors->first('registration') }}
            </div>
        @endif

        <form method="POST" action="{{ route('gym.register') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Gym Information Card -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <x-icon name="building-office" class="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Gym Information</h2>
                        <p class="text-xs sm:text-sm text-gray-500">Tell us about your gym.</p>
                    </div>
                </header>

                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Gym Name -->
                    <div class="sm:col-span-2">
                        <label for="gym_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Gym Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="gym_name" id="gym_name" value="{{ old('gym_name') }}" required
                            placeholder="e.g. PowerFit Gym"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('gym_name') border-red-500 @enderror">
                        @error('gym_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gym Phone -->
                    <div>
                        <label for="gym_phone" class="block text-sm font-medium text-gray-700 mb-1.5">Gym Phone</label>
                        <input type="tel" name="gym_phone" id="gym_phone" value="{{ old('gym_phone') }}"
                            placeholder="+1 234 567 8900"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('gym_phone') border-red-500 @enderror">
                        @error('gym_phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gym Email -->
                    <div>
                        <label for="gym_email" class="block text-sm font-medium text-gray-700 mb-1.5">Gym Email</label>
                        <input type="email" name="gym_email" id="gym_email" value="{{ old('gym_email') }}"
                            placeholder="contact@yourgym.com"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('gym_email') border-red-500 @enderror">
                        @error('gym_email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-2">
                        <label for="gym_address" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="gym_address" id="gym_address" value="{{ old('gym_address') }}"
                            required placeholder="Street address"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('gym_address') border-red-500 @enderror">
                        @error('gym_address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label for="gym_city" class="block text-sm font-medium text-gray-700 mb-1.5">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="gym_city" id="gym_city" value="{{ old('gym_city') }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('gym_city') border-red-500 @enderror">
                        @error('gym_city')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- State -->
                    <div>
                        <label for="gym_state" class="block text-sm font-medium text-gray-700 mb-1.5">
                            State <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="gym_state" id="gym_state" value="{{ old('gym_state') }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('gym_state') border-red-500 @enderror">
                        @error('gym_state')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Zip -->
                    <div>
                        <label for="gym_zip_code" class="block text-sm font-medium text-gray-700 mb-1.5">ZIP / Postal
                            Code</label>
                        <input type="text" name="gym_zip_code" id="gym_zip_code"
                            value="{{ old('gym_zip_code') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('gym_zip_code') border-red-500 @enderror">
                        @error('gym_zip_code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div class="sm:col-span-2">
                        <label for="gym_logo" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Gym Logo <span class="text-gray-400 text-xs font-normal">(optional, max 2MB)</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0"
                                id="logo-preview-container">
                                <x-icon name="photo" class="w-6 h-6 text-gray-400" />
                            </div>
                            <input type="file" name="gym_logo" id="gym_logo" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer @error('gym_logo') border-red-500 @enderror">
                        </div>
                        @error('gym_logo')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Owner Account Card -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                        <x-icon name="user" class="w-5 h-5 text-green-600" />
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Owner Account</h2>
                        <p class="text-xs sm:text-sm text-gray-500">You will use this to log in.</p>
                    </div>
                </header>

                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Owner Name -->
                    <div class="sm:col-span-2">
                        <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name') }}"
                            required placeholder="e.g. John Smith"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('owner_name') border-red-500 @enderror">
                        @error('owner_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Owner Email -->
                    <div>
                        <label for="owner_email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="owner_email" id="owner_email" value="{{ old('owner_email') }}"
                            required placeholder="you@example.com"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('owner_email') border-red-500 @enderror">
                        @error('owner_email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Owner Phone -->
                    <div>
                        <label for="owner_phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="owner_phone" id="owner_phone" value="{{ old('owner_phone') }}"
                            required placeholder="+1 234 567 8900"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('owner_phone') border-red-500 @enderror">
                        @error('owner_phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                            placeholder="Min 8 chars, mixed case, numbers"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2.5">
                    </div>
                </div>
            </section>

            <!-- Terms & Submit -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-6">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}
                        class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                    <span class="text-sm text-gray-700">
                        I agree to the <a href="#" class="text-indigo-600 underline">Terms of Service</a> and
                        <a href="#" class="text-indigo-600 underline">Privacy Policy</a>.
                    </span>
                </label>
                @error('terms')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-6 flex flex-col sm:flex-row-reverse gap-3">
                    <button type="submit" id="submit-btn"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-200 transition w-full sm:w-auto disabled:opacity-60 disabled:cursor-not-allowed">
                        <span id="submit-text">Create My Gym</span>
                        <x-icon name="arrow-right" class="w-5 h-5" />
                    </button>
                    <a href="/"
                        class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 w-full sm:w-auto">
                        Cancel
                    </a>
                </div>
            </section>
        </form>
    </main>

    <footer class="text-center py-6 text-xs text-gray-500">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>

    <script>
        // Logo preview
        document.getElementById('gym_logo')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const container = document.getElementById('logo-preview-container');
            if (file && container) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    container.innerHTML =
                        `<img src="${ev.target.result}" class="w-14 h-14 rounded-lg object-cover" alt="Logo preview">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Submit loading state
        document.querySelector('form')?.addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            const text = document.getElementById('submit-text');
            if (btn && text) {
                btn.disabled = true;
                text.textContent = 'Creating your gym...';
            }
        });
    </script>
</body>

</html>
