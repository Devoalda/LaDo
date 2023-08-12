@foreach($projects as $project)
<div class="relative">
    <a href="{{ route('project.todo.index', $project->id) }}"
       class="card-link">
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
