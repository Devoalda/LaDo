<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4 py-3">
    <h2 class="text-2xl font-semibold mb-4 text-green-500 dark:text-green-400">
        Today's Todos
        ({{ $incomplete_count }})
    </h2>
    <div class="space-y-4" id="todo-container">
        @foreach ($todos as $todo)
        @if ($todo->projects->isNotEmpty())
        @php
        $project = $todo->projects->first();
        $due = null;

        if ($todo->due_start && $todo->due_end) {
        $due = \Carbon\Carbon::parse($todo->due_end);
        } elseif ($todo->due_start) {
        $due = \Carbon\Carbon::parse($todo->due_start);
        } elseif ($todo->due_end) {
        $due = \Carbon\Carbon::parse($todo->due_end);
        }

        $now = now();
        $timeRemaining = $due ? ($due->isFuture() ? $now->diffForHumans($due, true) : $due->diffForHumans($now, true)) :
        null;
        @endphp

        <a href="{{ route('project.todo.edit', [$project->id, $todo->id]) }}" class="block">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                <div class="flex items-center">
                    <form action="{{ route('project.todo.update', [$project->id, $todo->id]) }}" method="POST"
                          class="toggle-completed-form">
                        @csrf
                        @method('PUT')
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="completed_at" id="completed_at_{{ $todo->id }}"
                                   class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                   onchange="this.form.submit()" {{ $todo->completed_at ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700"></span>
                        </label>
                    </form>
                    <span class="ml-2 text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $todo->title }}</span>
                </div>
                <div>
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


    @if($todos->hasMorePages())
    <div class="invisible">
        <button wire:click.prevent="loadMore"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Load more
        </button>
    </div>

    <div wire:loading>
        <div class="text-blue-900 dark:text-gray-100 font-bold py-2 px-4 rounded flex justify-center">
            Loading...
        </div>
    </div>

    <!-- Livewire script to trigger the load more button after user scrolls to the bottom of the page -->
    <script>
        window.onscroll = function (ev) {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                Livewire.emit('load-more');
                console.log('Load more');
            }
        };
    </script>

    @else
    <!-- If there are no more pages, show this message -->
    <div class="text-blue-900 dark:text-gray-100 font-bold py-2 px-4 rounded flex justify-center">
        Congratulations! You've reached the end of the list.
    </div>
    @endif

</div>
