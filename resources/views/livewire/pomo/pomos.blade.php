<div class="flex justify-center mt-4">
    <table class="table-fixed w-3/4 border rounded-lg dark:bg-gray-800">
        <thead class="bg-blue-100">
        <tr>
            <th class="px-4 py-2">Task</th>
            <th class="px-4 py-2">Start</th>
            <th class="px-4 py-2">End</th>
            <th class="px-4 py-2">Duration (minutes)</th>
            <th class="px-4 py-2">Notes</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
        </thead>
        <tbody id="pomo-container">
        @include('livewire.pomo.load-pomo')
        </tbody>
    </table>
</div>
