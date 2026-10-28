<x-owner-layout>
    <x-slot name="title">Add Trainer</x-slot>
    <x-slot name="headerTitle">Add Trainer</x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('owner.trainers.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Personal Info -->
            <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                        <x-icon name="user" class="w-5 h-5 text-green-600" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Personal Information</h2>
                        <p class="text-xs text-gray-500">Basic details and login credentials.</p>
                    </div>
                </header>
                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone <span
                                class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1.5">Date of
                            Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                    <div>
                        <label for="gender_id" class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                        <select name="gender_id" id="gender_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="">Select Gender</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->id }}"
                                    {{ old('gender_id') == $gender->id ? 'selected' : '' }}>{{ $gender->name }}
                                </option>
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
                                    {{ old('blood_group_id') == $bg->id ? 'selected' : '' }}>{{ $bg->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <textarea name="address" id="address" rows="2"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1.5">Profile Photo
                            <span class="text-gray-400 text-xs">(optional)</span></label>
                        <input type="file" name="profile_photo" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        @error('profile_photo')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Professional Info -->
            <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <x-icon name="clipboard" class="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Professional Details</h2>
                        <p class="text-xs text-gray-500">Qualifications and experience.</p>
                    </div>
                </header>
                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="specialization"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Specialization</label>
                        <input type="text" name="specialization" id="specialization"
                            value="{{ old('specialization') }}" placeholder="e.g. Strength, Yoga, CrossFit"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                    <div>
                        <label for="qualification"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Qualification</label>
                        <input type="text" name="qualification" id="qualification"
                            value="{{ old('qualification') }}" placeholder="e.g. ACE Certified"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                    <div>
                        <label for="experience_years"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Experience
                            (years)</label>
                        <input type="number" name="experience_years" id="experience_years"
                            value="{{ old('experience_years', 0) }}" min="0" max="50"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                    <div>
                        <label for="joining_date" class="block text-sm font-medium text-gray-700 mb-1.5">Joining Date
                            <span class="text-red-500">*</span></label>
                        <input type="date" name="joining_date" id="joining_date"
                            value="{{ old('joining_date', now()->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('joining_date') border-red-500 @enderror">
                        @error('joining_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row-reverse gap-3">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px]">
                    <x-icon name="check-circle" class="w-5 h-5" />
                    Add Trainer
                </button>
                <a href="{{ route('owner.trainers.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition min-h-[44px]">Cancel</a>
            </div>
        </form>
    </div>
</x-owner-layout>
