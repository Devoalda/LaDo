<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Todo List for Project:') }} <span class="font-bold text-blue-500 dark:text-blue-400"
            >{{ $project->name }}</span>
        </h2>
    </x-slot>

    <!-- Same width and design as the bottom todo listing -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4">
        <form method="POST" action="{{ route('project.todo.store', $project->id) }}">
            @csrf
            <div class="text-gray-800 dark:text-gray-100">
                <div class="mb-4 flex items-center">
                    <input type="text" name="title" id="title" placeholder="Your Awesome Todo"
                           class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-gray-100 rounded-lg w-full p-4 @error('title') border-red-500 @enderror"
                           value="{{ old('title') }}" required autofocus>

                    <!-- Plus button to submit the form or go to todo.create if title is empty -->
                    <button type="submit"
                            class="ml-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            onclick="event.preventDefault();
                            document.getElementById('title').value.trim() === '' ? window.location.href = '{{ route('project.todo.create', $project->id) }}' : this.form.submit();">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             class="w-6 h-full inline-block">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>
                @error('title')
                <div class="text-red-500 mt-2 text-sm">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </form>
    </div>


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4">
        <h2 class="text-2xl font-semibold mb-4 text-red-500 dark:text-red-400">
            Not Completed ({{ $todos->count() }})
        </h2>
        <div class="space-y-4">
            @foreach ($todos as $todo)
            @if (!$todo->completed_at)
            <a href="{{ route('project.todo.edit', [$project->id, $todo->id]) }}" class="block">:
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Checkbox to toggle completed at -->
                        <form action="{{ route('project.todo.update', [$project->id, $todo->id]) }}"
                              method="POST"
                              class="toggle-completed-form">
                            @csrf
                            @method('PUT')
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="completed_at"
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                       onclick="this.form.submit()" {{ $todo->completed_at ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700"></span>
                            </label>
                        </form>
                        <span class="ml-2 text-xl font-bold text-gray-800 dark:text-gray-100">{{ $todo->title }}</span>
                    </div>
                    <div>
                        @php
                        if ($todo->due_start && $todo->due_end) {
                        $due = \Carbon\Carbon::parse($todo->due_end);
                        $now = now();
                        $timeRemaining = $due->isFuture() ? $now->diffForHumans($due, true) : $due->diffForHumans($now,
                        true);
                        } elseif ($todo->due_start) {
                        $due = \Carbon\Carbon::parse($todo->due_start);
                        $now = now();
                        $timeRemaining = $due->isFuture() ? $now->diffForHumans($due, true) : $due->diffForHumans($now,
                        true);
                        } elseif ($todo->due_end) {
                        $due = \Carbon\Carbon::parse($todo->due_end);
                        $now = now();
                        $timeRemaining = $due->isFuture() ? $now->diffForHumans($due, true) : $due->diffForHumans($now,
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
            <a href="{{ route('project.todo.edit', [$project->id, $todo->id]) }}" class="block">
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <form action="{{ route('project.todo.update', [$project->id, $todo->id]) }}"
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
