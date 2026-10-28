<x-owner-layout>
    <x-slot name="title">Edit Member</x-slot>
    <x-slot name="headerTitle">Edit Member</x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('owner.members.update', $member) }}" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Personal Info -->
            <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                        <x-icon name="user" class="w-5 h-5 text-blue-600" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Personal Information</h2>
                    </div>
                </header>
                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name"
                            value="{{ old('name', $member->user->name) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email"
                            value="{{ old('email', $member->user->email) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone <span
                                class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone"
                            value="{{ old('phone', $member->user->phone) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1.5">Date of
                            Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth"
                            value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                    <div>
                        <label for="gender_id" class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                        <select name="gender_id" id="gender_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="">Select Gender</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->id }}"
                                    {{ old('gender_id', $member->gender_id) == $gender->id ? 'selected' : '' }}>
                                    {{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="blood_group_id" class="block text-sm font-medium text-gray-700 mb-1.5">Blood
                            Group</label>
                        <select name="blood_group_id" id="blood_group_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="">Select Blood Group</option>
                            @foreach ($bloodGroups as $bg)
                                <option value="{{ $bg->id }}"
                                    {{ old('blood_group_id', $member->blood_group_id) == $bg->id ? 'selected' : '' }}>
                                    {{ $bg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="joining_date" class="block text-sm font-medium text-gray-700 mb-1.5">Joining Date
                            <span class="text-red-500">*</span></label>
                        <input type="date" name="joining_date" id="joining_date"
                            value="{{ old('joining_date', $member->joining_date?->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <textarea name="address" id="address" rows="2"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">{{ old('address', $member->address) }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Profile Photo</label>
                        <div class="flex items-center gap-4">
                            @if ($member->profile_photo)
                                <img src="{{ asset('storage/' . $member->profile_photo) }}"
                                    class="w-14 h-14 rounded-full object-cover border">
                            @else
                                <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center"><span
                                        class="text-blue-700 font-bold text-lg">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <input type="file" name="profile_photo" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        </div>
                        @error('profile_photo')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Emergency Contact -->
            <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                        <x-icon name="phone" class="w-5 h-5 text-red-600" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Emergency Contact</h2>
                    </div>
                </header>
                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="emergency_contact_name"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Contact Name</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                            value="{{ old('emergency_contact_name', $member->emergency_contact_name) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                    <div>
                        <label for="emergency_contact_phone"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone"
                            value="{{ old('emergency_contact_phone', $member->emergency_contact_phone) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                </div>
            </section>

            <!-- Change Password -->
            <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                        <x-icon name="lock-closed" class="w-5 h-5 text-amber-600" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Change Password</h2>
                        <p class="text-xs text-gray-500">Leave blank to keep the current password.</p>
                    </div>
                </header>
                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">New
                            Password</label>
                        <input type="password" name="password" id="password"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                </div>
            </section>

            <!-- Read-only Info -->
            <section class="bg-gray-50 rounded-2xl border border-gray-200 p-5 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Member Code</p>
                        <p class="text-sm font-mono font-medium text-gray-700">{{ $member->member_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Account Status</p>
                        @php $isActive = $member->user->accountStatus?->code === 'active'; @endphp
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($member->user->accountStatus?->name ?? 'Unknown') }}
                        </span>
                    </div>
                </div>
            </section>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row-reverse gap-3">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px]">
                    <x-icon name="check-circle" class="w-5 h-5" /> Save Changes
                </button>
                <a href="{{ route('owner.members.show', $member) }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition min-h-[44px]">Cancel</a>
            </div>
        </form>
    </div>
</x-owner-layout>
