<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __($editing ? 'Edit' : 'Create') }} {{ __('Pomo') }}

        </h2>
    </x-slot>

    <livewire:pomo.create :pomo="$pomo" :editing="$editing">

</x-app-layout>


