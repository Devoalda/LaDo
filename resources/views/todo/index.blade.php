<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Todo List') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-3">
                    <form method="POST" action="{{ route('todo.store') }}" id="todo-form">
                        @csrf
                        <div class="text-gray-800 dark:text-gray-100">
                            <div class="mb-4">
                                <!-- Submit only when user presses enter -->
                                <input type="text" name="title" id="title" placeholder="Your Awesome Todo"
                                       class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-gray-100 rounded-lg w-full p-4 @error('title') border-red-500 @enderror"
                                       value="{{ old('title') }}" required autofocus>
                                @error('title')
                                <div class="text-red-500 mt-2 text-sm">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-3">
                    <h2 class="text-2xl font-semibold mb-4 text-red-500 dark:text-red-400">
                        Not Completed ({{ $todos->count() }})
                    </h2>
                    <div class="space-y-4">
                        @foreach ($todos as $todo)
                        @if (!$todo->completed_at)
                        <a href="{{ route('todo.edit', $todo->id) }}" class="block">
                            <div
                                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                                <div class="flex items-center">
                                    <!-- Checkbox to toggle completed at -->
                                    <form action="{{ route('todo.update', $todo->id) }}" method="POST"
                                          class="toggle-completed-form">
                                        @csrf
                                        @method('PUT')
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="completed_at"
                                                   class="form-checkbox h-5 w-5 text-blue-600"
                                                   onclick="this.form.submit()" {{ $todo->completed_at ? 'checked' : ''
                                            }}>
                                            <span class="ml-2 text-sm text-gray-700"></span>
                                        </label>

                                    </form>
                                    <span class="ml-2 text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $todo->title }}</span>
                                </div>
                                <div>
                                    @php
                                    $due_start = $todo->due_start ? \Carbon\Carbon::parse($todo->due_start) : null;
                                    $due_end = $todo->due_end ? \Carbon\Carbon::parse($todo->due_end) : null;

                                    if ($due_start && $due_end) {
                                    $time_remaining = $due_end->diffForHumans($due_start, true);
                                    $time_remaining_in_hours = $due_end->diffInHours($due_start);
                                    } elseif ($due_start) {
                                    $time_remaining = $due_start->diffForHumans(\Carbon\Carbon::now(), true);
                                    $time_remaining_in_hours = $due_start->diffInHours(\Carbon\Carbon::now());
                                    } elseif ($due_end) {
                                    $time_remaining = $due_end->diffForHumans(\Carbon\Carbon::now(), true);
                                    $time_remaining_in_hours = $due_end->diffInHours(\Carbon\Carbon::now());
                                    }
                                    @endphp

                                    @if ($due_start || $due_end)
                                    @if ($due_start && $due_end)
                                    @if ($due_start == $due_end)
                                    <p class="text-sm text-gray-500 mb-2">Due: {{ $due_start->format('Y-m-d H:i:s')
                                        }}</p>
                                    @else
                                    <p class="text-sm text-gray-500 mb-2">Due: {{ $due_start->format('Y-m-d H:i:s') }} -
                                        {{ $due_end->format('Y-m-d H:i:s') }}</p>
                                    @endif
                                    @else
                                    @if ($due_start)
                                    <p class="text-sm text-gray-500 mb-2">Due: {{ $due_start->format('Y-m-d H:i:s')
                                        }}</p>
                                    @elseif ($due_end)
                                    <p class="text-sm text-gray-500 mb-2">Due: {{ $due_end->format('Y-m-d H:i:s') }}</p>
                                    @endif
                                    @endif

                                    @if (isset($time_remaining))
                                    @if ($due_end && $due_end->diffInDays() < 1)
                                    <p class="text-sm text-red-600">{{ $time_remaining }}
                                        <!--                                        {{ $time_remaining_in_hours > 0 ? '(' . $time_remaining_in_hours . ' hours)' : '' }} -->
                                        ago</p>
                                    @elseif ($due_end && $due_end->diffInDays() < 2)
                                    <p class="text-sm text-orange-600">{{ $time_remaining }} {{ $time_remaining_in_hours
                                        > 0 ? '(' . $time_remaining_in_hours . ' hours)' : '' }} remaining</p>
                                    @else
                                    <p class="text-sm text-green-600">{{ $time_remaining }} remaining</p>
                                    @endif
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>


                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-4 text-green-500 dark:text-green-400">
                        Completed Today
                        ({{ $todos->where('completed_at', '>=', \Carbon\Carbon::today())->count() }})
                    </h2>
                    <div class="space-y-4">
                        @foreach ($todos as $todo)
                        @if ($todo->completed_at)
                        <a href="{{ route('todo.edit', $todo->id) }}" class="block">
                            <div
                                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                                <div class="flex items-center">
                                    <form action="{{ route('todo.update', $todo->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="completed_at" id="completed_at_{{ $todo->id }}"
                                                   class="form-checkbox h-5 w-5 text-blue-600"
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
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
