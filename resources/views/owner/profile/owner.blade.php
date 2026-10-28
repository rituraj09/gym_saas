<x-owner-layout>
    <x-slot name="title">My Profile</x-slot>
    <x-slot name="headerTitle">My Profile</x-slot>

    <div class="max-w-3xl space-y-6">

        <!-- Personal Information -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <x-icon name="user" class="w-5 h-5 text-green-600" />
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Personal Information</h2>
                    <p class="text-xs text-gray-500">Update your name, contact details, and photo.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('owner.my-profile.update') }}" enctype="multipart/form-data"
                class="p-5 sm:p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <!-- Profile Photo -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                        <div class="flex items-center gap-4">
                            @if ($owner->profile_photo)
                                <img src="{{ asset('storage/' . $owner->profile_photo) }}" alt="Profile"
                                    class="w-16 h-16 rounded-full object-cover border border-gray-200">
                            @else
                                <div
                                    class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center border border-gray-200">
                                    <span
                                        class="text-indigo-700 font-bold text-xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <input type="file" name="profile_photo" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG or WebP. Max 2MB.</p>
                            </div>
                        </div>
                        @error('profile_photo')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Full Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone <span
                                class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1.5">Date of
                            Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth"
                            value="{{ old('date_of_birth', $owner->date_of_birth?->format('Y-m-d')) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Owner Code (read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Owner Code</label>
                        <input type="text" value="{{ $owner->owner_code }}" disabled
                            class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 py-2.5 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Auto-generated. Cannot be changed.</p>
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <textarea name="address" id="address" rows="2"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('address') border-red-500 @enderror">{{ old('address', $owner->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
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

        <!-- Change Password -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <x-icon name="lock-closed" class="w-5 h-5 text-amber-600" />
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Change Password</h2>
                    <p class="text-xs text-gray-500">Leave blank to keep your current password.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('owner.my-profile.update') }}" class="p-5 sm:p-6">
                @csrf
                @method('PUT')

                <!-- Hidden fields required by validation -->
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="phone" value="{{ $user->phone }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">New
                            Password</label>
                        <input type="password" name="password" id="password"
                            placeholder="Min 8 chars, mixed case, numbers"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 text-white text-sm font-semibold rounded-xl hover:bg-amber-700 transition shadow-sm">
                        <x-icon name="lock-closed" class="w-4 h-4" />
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Info (read-only) -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                    <x-icon name="clipboard" class="w-5 h-5 text-gray-500" />
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Account Information</h2>
                    <p class="text-xs text-gray-500">Read-only details about your account.</p>
                </div>
            </div>

            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Account Status</p>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        {{ ucfirst($user->accountStatus?->name ?? 'Active') }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Role</p>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Gym Owner
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Member Since</p>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $owner->joining_date?->format('M d, Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Gym</p>
                    <p class="text-sm font-medium text-gray-900">{{ $owner->gym->name }}</p>
                </div>
            </div>
        </div>
    </div>
</x-owner-layout>
