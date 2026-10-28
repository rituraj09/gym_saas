<x-owner-layout>
    <x-slot name="title">Gym Profile</x-slot>
    <x-slot name="headerTitle">Gym Profile</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">

            <!-- Header -->
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <x-icon name="building-office" class="w-5 h-5 text-indigo-600" />
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Gym Information</h2>
                    <p class="text-xs text-gray-500">Update your gym's public details.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('owner.gym-profile.update') }}" enctype="multipart/form-data"
                class="p-5 sm:p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <!-- Current Logo -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gym Logo</label>
                        <div class="flex items-center gap-4">
                            @if ($gym->logo)
                                <img src="{{ asset('storage/' . $gym->logo) }}" alt="Gym Logo"
                                    class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                            @else
                                <div
                                    class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center border border-gray-200">
                                    <x-icon name="photo" class="w-7 h-7 text-gray-400" />
                                </div>
                            @endif
                            <div>
                                <input type="file" name="logo" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG or WebP. Max 2MB.</p>
                            </div>
                        </div>
                        @error('logo')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gym Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Gym Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $gym->name) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $gym->email) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $gym->phone) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Address <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="address" id="address" value="{{ old('address', $gym->address) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('address') border-red-500 @enderror">
                        @error('address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1.5">City <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="city" id="city" value="{{ old('city', $gym->city) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('city') border-red-500 @enderror">
                        @error('city')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- State -->
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1.5">State <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="state" id="state" value="{{ old('state', $gym->state) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('state') border-red-500 @enderror">
                        @error('state')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Zip -->
                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1.5">ZIP Code</label>
                        <input type="text" name="zip_code" id="zip_code"
                            value="{{ old('zip_code', $gym->zip_code) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('zip_code') border-red-500 @enderror">
                        @error('zip_code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gym Code (read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Gym Code</label>
                        <input type="text" value="{{ $gym->code }}" disabled
                            class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 py-2.5 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Auto-generated. Cannot be changed.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                        <x-icon name="check-circle" class="w-4 h-4" />
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-owner-layout>
