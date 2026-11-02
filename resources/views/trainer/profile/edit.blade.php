<x-trainer-layout>
    <x-slot name="headerTitle">My Profile</x-slot>

    <div class="max-w-2xl mx-auto space-y-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <x-icon name="user-circle" class="w-5 h-5 text-indigo-500" />
                    Profile Information
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                        <div class="px-4 py-2.5 bg-gray-50 rounded-xl border border-gray-200 font-medium text-gray-900">
                            {{ $user->name }}</div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <div class="px-4 py-2.5 bg-gray-50 rounded-xl border border-gray-200 font-medium text-gray-900">
                            {{ $user->email }}</div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                        <div class="px-4 py-2.5 bg-gray-50 rounded-xl border border-gray-200 text-gray-900">
                            {{ $user->phone ?? '—' }}</div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-500 mb-1">Gym</label>
                        <div class="px-4 py-2.5 bg-gray-50 rounded-xl border border-gray-200 text-gray-900">
                            {{ $trainer->gym?->name ?? '—' }}</div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-500 mb-1">Gender</label>
                        <div class="px-4 py-2.5 bg-gray-50 rounded-xl border border-gray-200 text-gray-900">
                            {{ $trainer->gender?->name ?? '—' }}</div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-500 mb-1">Blood Group</label>
                        <div class="px-4 py-2.5 bg-gray-50 rounded-xl border border-gray-200 text-gray-900">
                            {{ $trainer->bloodGroup?->name ?? '—' }}</div>
                    </div>
                </div>
                <p class="text-sm text-gray-400">Contact your gym owner to update personal information.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <x-icon name="lock-closed" class="w-5 h-5 text-indigo-500" />
                    Change Password
                </h2>
            </div>
            <form method="POST" action="{{ route('trainer.profile.password') }}" class="p-6 space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Current Password <span
                            class="text-red-500">*</span></label>
                    <input type="password" name="current_password" required
                        class="w-full px-4 py-3 rounded-xl border @error('current_password') border-red-400 @else border-gray-300 @enderror min-h-[44px]">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password <span
                            class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full px-4 py-3 rounded-xl border @error('password') border-red-400 @else border-gray-300 @enderror min-h-[44px]">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password <span
                            class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="8"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 min-h-[44px]">
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition min-h-[44px]">
                    <x-icon name="lock-closed" class="w-4 h-4" />
                    Update Password
                </button>
            </form>
        </div>
    </div>
</x-trainer-layout>
