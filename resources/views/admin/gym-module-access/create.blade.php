<x-admin-layout>
    <x-slot name="title">Create Module Override</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.gym-module-access.index', $gym) }}"
            class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-2">
            <x-icon name="arrow-left" class="w-4 h-4" /> Back to Module Access
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create Module Override</h1>
        <p class="text-sm text-gray-500 mt-1">
            Override the plan default for <strong>{{ $gym->name }}</strong>.
        </p>
    </div>

    @if (count($availableModules) === 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <x-icon name="check-circle" class="w-10 h-10 text-yellow-500 mx-auto mb-2" />
            <p class="text-gray-700 font-medium">All modules already have overrides.</p>
            <a href="{{ route('admin.gym-module-access.index', $gym) }}"
                class="text-indigo-600 text-sm mt-2 inline-block hover:underline">← Back to Module Access</a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-xl">
            <form method="POST" action="{{ route('admin.gym-module-access.store', $gym) }}">
                @csrf
                <input type="hidden" name="gym_id" value="{{ $gym->id }}">

                {{-- Module --}}
                <div class="mb-5">
                    <label for="module_code" class="block text-sm font-medium text-gray-700 mb-1">
                        Module <span class="text-red-500">*</span>
                    </label>
                    <select name="module_code" id="module_code" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('module_code') border-red-500 @enderror">
                        <option value="">— Select Module —</option>
                        @foreach ($availableModules as $code => $label)
                            <option value="{{ $code }}" {{ request('module') === $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('module_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Enable / Disable --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Access <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer min-h-[44px]">
                            <input type="radio" name="is_enabled" value="1"
                                {{ old('is_enabled', '1') === '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-gray-700">
                                <span class="font-medium text-green-700">Enable</span>
                                — Grant access to this module
                            </span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer min-h-[44px]">
                            <input type="radio" name="is_enabled" value="0"
                                {{ old('is_enabled') === '0' ? 'checked' : '' }}
                                class="w-4 h-4 text-red-600 focus:ring-red-500">
                            <span class="text-sm text-gray-700">
                                <span class="font-medium text-red-700">Disable</span>
                                — Revoke access to this module
                            </span>
                        </label>
                    </div>
                    @error('is_enabled')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Reason --}}
                <div class="mb-5">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                        Reason
                        <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea name="reason" id="reason" rows="2" maxlength="500"
                        placeholder="e.g., Free 30-day trial of Diet Plans"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Expiry --}}
                <div class="mb-6">
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">
                        Expires At
                        <span class="text-gray-400 font-normal">(optional — leave blank for permanent)</span>
                    </label>
                    <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                        min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('expires_at') border-red-500 @enderror">
                    @error('expires_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">
                        After this date, the override is ignored and the plan default takes over.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                        <x-icon name="check-circle" class="w-4 h-4" />
                        Create Override
                    </button>
                    <a href="{{ route('admin.gym-module-access.index', $gym) }}"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition min-h-[44px]">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    @endif
</x-admin-layout>
