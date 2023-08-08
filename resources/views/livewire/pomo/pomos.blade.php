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
        <tbody>
        @foreach ($pomos as $pomo)
        <tr class="hover:bg-blue-100 dark:hover:bg-gray-700">
            <td class="border px-4 py-2 text-blue-900 dark:text-gray-100">
                <a href="{{ route('project.todo.edit', ['project' => $pomo->todo->project->id, 'todo' => $pomo->todo->id]) }}">
                    {{ $pomo->todo->title }}
                </a>
            </td>
            <!-- Pomo Start and Pomo End -->
            <td class="border px-4 py-2 text-blue-900 dark:text-gray-100">
                {{ \Carbon\Carbon::createFromTimestamp($pomo->pomo_start)->format('Y-m-d H:i:s') }}
            </td>
            <td class="border px-4 py-2 text-blue-900 dark:text-gray-100">
                {{ \Carbon\Carbon::createFromTimestamp($pomo->pomo_end)->format('Y-m-d H:i:s') }}
            </td>
            <!-- Duration -->
            <td class="border px-4 py-2 text-blue-900 dark:text-gray-100">
                {{
                \Carbon\Carbon::createFromTimestamp($pomo->pomo_start)->diffInMinutes(\Carbon\Carbon::createFromTimestamp($pomo->pomo_end))
                }}
            </td>
            <td class="border px-4 py-2 text-blue-900 dark:text-gray-100">
                <!-- Truncate notes to 32 characters -->
                <div class="max-w-sm truncate">
                    {{ $pomo->notes }}
                </div>
            </td>
            <td class="border px-4 py-2 text-blue-900 dark:text-gray-100">
                <!-- Edit and Delete form button groups -->
                <div class="flex flex-row">
                    <div class="flex flex-col">
                        <a href="{{ route('pomo.edit', $pomo->id) }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                    </div>
                    <div class="flex flex-col">
                        <form action="{{ route('pomo.destroy', $pomo->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete
                            </button>
                        </form>
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
