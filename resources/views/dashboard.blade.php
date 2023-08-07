<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Display Statistics of Todo Completion -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4">
        <h2 class="text-2xl font-semibold mb-4 text-indigo-500 dark:text-indigo-400">
            <!-- Icon for Target board -->
            <svg class="inline-block h-6 w-6 text-indigo-500 dark:text-indigo-400"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 9v6m0 0v6m0-6h6m-6 0H3"/>
            </svg>
            Your Stats
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4 text-red-500 dark:text-red-400">
                    <!-- SVG for Incomplete Todos -->
                    <svg class="inline-block h-6 w-6 text-red-500 dark:text-red-400"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Total incomplete Todos
                </h3>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $incomplete_count }}
                </p>
            </div>

            <!-- Todo Completed Count -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4 text-green-500 dark:text-green-400">
                    <!-- SVG for Completed Todos -->
                    <svg class="inline-block h-6 w-6 text-green-500 dark:text-green-400"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    Total Completed Todos
                </h3>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $todo_completed_count }}
                </p>
            </div>

            <!-- Average Todo Count per Project -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4 text-yellow-500 dark:text-yellow-400">
                    <!-- SVG for Average Todos -->
                    <svg class="inline-block h-6 w-6 text-yellow-500 dark:text-yellow-400"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Average Todos per Project
                </h3>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $ave_todo_count }}
                </p>
            </div>

            <!-- Project Count -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4 text-blue-500 dark:text-blue-400">
                    <!-- SVG for Project Count -->
                    <svg class="inline-block h-6 w-6 text-blue-500 dark:text-blue-400"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Total Projects
                </h3>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $project_count }}
                </p>
            </div>

        </div>

    </div>


    <!-- List out Todos and their details (time left/ago) + checkbox in form to toggle completed -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4">
        <h2 class="text-2xl font-semibold mb-4 text-green-500 dark:text-green-400">
            Today's Todos
            ({{ $incomplete_count }})
        </h2>
        <div class="space-y-4">
            @foreach ($todos as $todo)
            <a href="{{ route('project.todo.edit', [$todo->project->id, $todo->id]) }}" class="block">
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
            @endforeach
        </div>
    </div>

    <!-- Pagination with CSS -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4 pb-4">
        {{ $todos->links() }}
    </div>

</x-app-layout>
