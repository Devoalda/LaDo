<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Project List') }}
            <a href="{{ route('project.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded float-right">
                Create Project
            </a>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($projects as $project)
                <div class="relative">
                    <a href="{{ route('project.todo.index', $project) }}" class="card-link">
                        <div
                            class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 hover:shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1">
                            <div class="text-gray-800 dark:text-gray-100">
                                <div class="mb-4">
                                    <h3 class="font-semibold text-lg mb-2">{{ $project->name }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $project->description }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                    <form action="{{ route('project.destroy', $project) }}" method="POST"
                          class="delete-project-form absolute top-1 right-1">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                class="delete-button text-red-600 hover:text-red-800 transition duration-300 ease-in-out">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div class="modal hidden">
                            <!-- Small Popover, with a background that is visible when modal is open -->
                            <div class="popover popover-sm bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                                <p class="mb-4">Are you sure you want to delete this project?</p>
                                <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mr-2">
                                    Delete
                                </button>
                                <button type="button"
                                        class="cancel-button bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        const deleteButtons = document.querySelectorAll('.delete-button');
        const cancelButtons = document.querySelectorAll('.cancel-button');
        const modals = document.querySelectorAll('.modal');

        deleteButtons.forEach((deleteButton, index) => {
            deleteButton.addEventListener('click', () => {
                modals[index].classList.remove('hidden');
            });

            cancelButtons[index].addEventListener('click', () => {
                modals[index].classList.add('hidden');
            });
        });
    </script>


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4">
        <h2 class="text-2xl font-semibold mb-4 text-red-500 dark:text-red-400">
            Not Completed ({{ $todos->count() }})
        </h2>
        <div class="space-y-4">
            @foreach ($todos as $todo)
            @if (!$todo->completed_at)
            <a href="{{ route('project.todo.edit', [$todo->project->id, $todo->id]) }}" class="block">
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Checkbox to toggle completed at -->
                        <form action="{{ route('project.todo.update', [$todo->project->id, $todo->id]) }}"
                              method="POST"
                              class="toggle-completed-form">
                            @csrf
                            @method('PUT')
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="completed_at"
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                       onclick="this.form.submit()" {{ $todo->completed_at ? 'checked' : ''
                                }}>
                                <span class="ml-2 text-sm text-gray-700"></span>
                            </label>
                        </form>
                        <span
                            class="ml-2 text-xl font-bold text-gray-800 dark:text-gray-100">{{ $todo->title }}
                            <!-- Project name and link to project beside todo title as a badge with a blue background -->
                            <span
                                class="ml-2 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full px-2 py-1">{{ $todo->project->name }}</span>
                            </span>
                    </div>
                    <div>
                        @php
                        if ($todo->due_start && $todo->due_end) {
                        $due = \Carbon\Carbon::parse($todo->due_end);
                        $now = now();
                        $timeRemaining = $due->isFuture() ? $now->diffForHumans($due, true) :
                        $due->diffForHumans($now,
                        true);
                        } elseif ($todo->due_start) {
                        $due = \Carbon\Carbon::parse($todo->due_start);
                        $now = now();
                        $timeRemaining = $due->isFuture() ? $now->diffForHumans($due, true) :
                        $due->diffForHumans($now,
                        true);
                        } elseif ($todo->due_end) {
                        $due = \Carbon\Carbon::parse($todo->due_end);
                        $now = now();
                        $timeRemaining = $due->isFuture() ? $now->diffForHumans($due, true) :
                        $due->diffForHumans($now,
                        true);
                        } else {
                        // If there is no due_start or due_end, set $timeRemaining to null
                        $timeRemaining = null;
                        }
                        @endphp

                        @if ($timeRemaining !== null)
                        @if ($due->isFuture())
                        <p class="text-sm text-green-600">{{ $timeRemaining }} remaining</p>
                        @else
                        <p class="text-sm text-red-600">{{ $timeRemaining }} ago</p>
                        @endif
                        @endif
                    </div>

                </div>
            </a>
            @endif
            @endforeach
        </div>
    </div>


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4">
        <h2 class="text-2xl font-semibold mb-4 text-green-500 dark:text-green-400">
            Completed Today
            ({{ $completed->count() }})
        </h2>
        <div class="space-y-4">
            @foreach ($completed as $todo)
            <a href="{{ route('project.todo.edit', [$todo->project->id, $todo->id]) }}"
               class="block">
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <form action="{{ route('project.todo.update', [$todo->project->id, $todo->id]) }}"
                              method="POST"
                              class="toggle-completed-form">
                            @csrf
                            @method('PUT')
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="completed_at" id="completed_at_{{ $todo->id }}"
                                       class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                       onchange="this.form.submit()" {{ $todo->completed_at ? 'checked' : ''
                                }}>
                                <span class="ml-2 text-sm text-gray-700"></span>
                            </label>
                        </form>
                        <span
                            class="ml-2 text-2xl font-bold text-gray-800 dark:text-gray-100">
                                        {{ $todo->title }}
                                    </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

</x-app-layout>
