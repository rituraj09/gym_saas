<x-app-layout>
    <x-slot name="title">Trainer Dashboard</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Trainer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Welcome, Trainer</h3>
                    <p class="text-gray-600">View your assigned members, create exercise and diet plans from here.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
