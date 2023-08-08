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
