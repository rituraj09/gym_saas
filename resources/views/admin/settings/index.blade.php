<x-admin-layout>
    <x-slot name="title">Platform Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Platform Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Configure global platform settings</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        @foreach ($settings as $group => $items)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 capitalize">
                        {{ str_replace('_', ' ', $group) }}
                    </h3>
                </div>
                <div class="p-6 space-y-5">
                    @foreach ($items as $setting)
                        <div>
                            <label for="setting_{{ $setting['key'] }}"
                                class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $setting['label'] }}
                            </label>

                            @if ($setting['type'] === 'boolean')
                                <div class="flex items-center gap-3 min-h-[44px]">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="settings[{{ $setting['key'] }}]" value="0">
                                        <input type="checkbox" name="settings[{{ $setting['key'] }}]" value="1"
                                            {{ $setting['value'] === '1' ? 'checked' : '' }} class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                        </div>
                                    </label>
                                    <span class="text-sm text-gray-500">
                                        {{ $setting['value'] === '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            @elseif($setting['type'] === 'number')
                                <input type="number" name="settings[{{ $setting['key'] }}]"
                                    id="setting_{{ $setting['key'] }}" value="{{ $setting['value'] }}"
                                    class="w-full sm:w-48 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 min-h-[44px]">
                            @elseif($setting['type'] === 'email')
                                <input type="email" name="settings[{{ $setting['key'] }}]"
                                    id="setting_{{ $setting['key'] }}" value="{{ $setting['value'] }}"
                                    class="w-full sm:w-80 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 min-h-[44px]">
                            @else
                                <input type="text" name="settings[{{ $setting['key'] }}]"
                                    id="setting_{{ $setting['key'] }}" value="{{ $setting['value'] }}"
                                    class="w-full sm:w-80 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 min-h-[44px]">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                <x-icon name="check-circle" class="w-4 h-4" />
                Save Settings
            </button>
        </div>
    </form>
</x-admin-layout>
