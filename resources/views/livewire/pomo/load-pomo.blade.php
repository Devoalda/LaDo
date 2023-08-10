<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 max-w-7xl mx-auto py-3">
    @foreach ($pomos as $pomo)
    <div class="border rounded-lg p-4 shadow-md hover:bg-blue-100 dark:hover:bg-gray-700">
        <div class="mb-2">
            <a href="{{ route('project.todo.edit', ['project' => $pomo->todo->project->id, 'todo' => $pomo->todo->id]) }}"
                class="text-blue-900 dark:text-gray-100 font-bold hover:underline">
                {{ $pomo->todo->title }}
            </a>
        </div>
        <div class="mb-2 text-blue-900 dark:text-gray-100">
            <strong>Start:</strong> {{ \Carbon\Carbon::createFromTimestamp($pomo->pomo_start)->format('d/m/Y H:i') }}
        </div>
        <div class="mb-2 text-blue-900 dark:text-gray-100">
            <strong>End:</strong> {{ \Carbon\Carbon::createFromTimestamp($pomo->pomo_end)->format('d/m/Y H:i') }}
        </div>
        <div class="mb-2 text-blue-900 dark:text-gray-100">
            <strong>Duration:</strong>
            {{ \Carbon\Carbon::createFromTimestamp($pomo->pomo_end)
                ->diff(\Carbon\Carbon::createFromTimestamp($pomo->pomo_start))
                ->format('%H h %I m')
            }}
        </div>
        <div class="mb-2 truncate text-blue-900 dark:text-gray-100">
            <strong>Notes:</strong> {{ $pomo->notes }}
        </div>
        <div class="flex justify-between">
            <a href="{{ route('pomo.edit', $pomo->id) }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</a>
            <form action="{{ route('pomo.destroy', $pomo->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

@if($pomos->hasMorePages())
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
    window.onscroll = function(ev) {
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


