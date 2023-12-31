<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-4">
        <!-- Incomplete Todos Section -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <h2 class="text-2xl font-semibold mb-4 text-red-500 dark:text-red-400 px-6 pt-6">
                Not Completed ({{ $todos->count() }})
            </h2>
            <div class="space-y-4 mb-4">
                <div class="space-y-4" id="todo-container">
                    @foreach ($todos as $todo)
                    @if ($todo->projects->isNotEmpty())
                    @php
                    $project = $todo->projects->first();
                    @endphp
                    @endif
                    @if (!$todo->completed_at)
                    @php
                    $due = null;

                    if ($todo->due_start && $todo->due_end) {
                    $due = \Carbon\Carbon::parse($todo->due_end);
                    } elseif ($todo->due_start) {
                    $due = \Carbon\Carbon::parse($todo->due_start);
                    } elseif ($todo->due_end) {
                    $due = \Carbon\Carbon::parse($todo->due_end);
                    }

                    $now = now();
                    $timeRemaining = $due ? ($due->isFuture() ? $now->diffForHumans($due, true) :
                    $due->diffForHumans($now, true)) : null;
                    @endphp

                    <a href="{{ route('project.todo.edit', [$project->id, $todo->id]) }}" class="block">
                        <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg flex items-center">
                            <form action="{{ route('project.todo.update', [$project->id, $todo->id]) }}"
                                  method="POST" class="toggle-completed-form">
                                @csrf
                                @method('PUT')
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="completed_at"
                                           class="w-6 h-6 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                           onclick="this.form.submit()" {{ $todo->completed_at ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700"></span>
                                </label>
                            </form>
                            <span
                                class="ml-2 text-xl font-bold text-gray-800 dark:text-gray-100">{{ $todo->title }}</span>
                            <div
                                class="ml-8 mt-2 py-1 px-2 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full w-64 truncate">
                                {{ $project->name }}
                            </div>
                        </div>

                        <div class="relative px-6 pb-6">
                            @if ($timeRemaining !== null)
                            <p class="text-sm {{ $due->isFuture() ? 'text-green-600' : 'text-red-600' }}">{{
                                $timeRemaining }} {{ $due->isFuture() ? 'remaining' : 'ago' }}</p>
                            @endif
                        </div>
                    </a>
                    @endif
                    @endforeach
                </div>

            </div>
        </div>

        <!-- Completed Todos Section -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <h2 class="text-2xl font-semibold mb-4 text-green-500 dark:text-green-400 px-6 pt-6">
                Completed ({{ $completed->count() }})
            </h2>
            <div class="space-y-4">
                @foreach ($completed as $todo)
                @if ($todo->projects->isNotEmpty())
                @php
                $project = $todo->projects->first();
                @endphp
                @endif
                <a href="{{ route('project.todo.edit', [$project->id, $todo->id]) }}"
                   class="block">
                    <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg flex items-center">
                        <form action="{{ route('project.todo.update', [$project->id, $todo->id]) }}"
                              method="POST"
                              class="toggle-completed-form">
                            @csrf
                            @method('PUT')
                            <label class="flex items-center cursor-pointer">
                                <!-- Larger checkbox for completed -->
                                <input type="checkbox" name="completed_at" id="completed_at_{{ $todo->id }}"
                                       class="w-6 h-6 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                       onchange="this.form.submit()" {{ $todo->completed_at ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700"></span>
                            </label>
                        </form>
                        <span class="ml-2 text-xl font-bold text-gray-800 dark:text-gray-100">
                                {{ $todo->title }}
                            </span>
                        <!-- Badge -->
                        <div
                            class="ml-8 mt-2 py-1 px-2 text-sm font-semibold text-green-600 bg-green-100 rounded-full w-64 truncate">
                            {{ $project->name }}
                        </div>
                    </div>

                    <!-- Date all Green -->
                    <div class="relative px-6 pb-6">
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
                        <p class="text-sm text-green-600">{{ $timeRemaining }} ago</p>
                        @endif
                        @endif

                        <!-- Completed at time -->
                        @if ($todo->completed_at)
                        <!-- Print at the right side of the div -->
                        <p class="text-sm text-gray-600 dark:text-gray-400 absolute top-0 right-0 mr-6">
                            Completed {{ \Carbon\Carbon::parse($todo->completed_at)->diffForHumans() }}
                        </p>
                    </div>

                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
