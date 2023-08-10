<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pomodoro Dashboard') }}
            <a href="{{ route('pomo.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded float-right">Create Pomo</a>

        </h2>
    </x-slot>

    <livewire:pomo.timer />

    <livewire:pomo.pomos />

</x-app-layout>
