<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Todo') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <form method="POST" action="{{ route('todo.update', $todo) }}" id="todo-form">
            @csrf
            @method('PUT')
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <div class="text-gray-800 dark:text-gray-100">

                        <div class="mb-4">
                            <label for="title" class="block mb-2 font-semibold">Title</label>
                            <input type="text" name="title" id="title" placeholder="Title"
                                   class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('title') border-red-500 @enderror"
                                   value="{{ old('title', $todo->title) }}">
                            @error('title')
                            <div class="text-red-500 mt-2 text-sm">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block mb-2 font-semibold">Description</label>
                            <textarea name="description" id="description" placeholder="Description"
                                      class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('description') border-red-500 @enderror">{{ old('description', $todo->description) }}</textarea>
                            @error('description')
                            <div class="text-red-500 mt-2 text-sm">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="due_start" class="block mb-2 font-semibold">Due Start</label>
                                <input type="datetime-local" name="due_start" id="due_start" placeholder="Due Start"
                                       class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('due_start') border-red-500 @enderror"
                                       value="{{ old('due_start', $todo->due_start ? date('Y-m-d\TH:i', strtotime($todo->due_start)) : '') }}">
                                @error('due_start')
                                <div class="text-red-500 mt-2 text-sm">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div>
                                <label for="due_end" class="block mb-2 font-semibold">Due End</label>
                                <input type="datetime-local" name="due_end" id="due_end" placeholder="Due End"
                                       class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('due_end') border-red-500 @enderror"
                                       value="{{ old('due_end', $todo->due_end ? date('Y-m-d\TH:i', $todo->due_end) : '') }}">
                                @error('due_end')
                                <div class="text-red-900 mt-2 text-sm">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-4 space-x-4">
                            <!-- Cancel Button (GET request to index route) -->
                            <a href="{{ route('todo.index') }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-transparent border border-gray-900 rounded-l-lg hover:bg-gray-900 hover:text-white focus:z-10 focus:ring-2 focus:ring-gray-500 focus:bg-gray-900 focus:text-white dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:bg-gray-700">
                                <svg class="w-5 h-5 mr-2" aria-hidden="true" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Cancel
                            </a>

                            <!-- Delete Button -->
                            <button type="button"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-800 focus:z-10 focus:ring-2 focus:ring-red-500 border border-grey-900 dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-red-700 dark:focus:bg-red-700"
                                    onclick="confirmDelete()">
                                <svg class="w-5 h-5 mr-2" aria-hidden="true" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Delete
                            </button>

                            <!-- Update Button -->
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-r-md hover:bg-green-800 focus:z-10 focus:ring-2 focus:ring-green-500 border border-grey-900 dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-green-700 dark:focus:bg-green-700">
                                <svg class="w-5 h-5 mr-2" aria-hidden="true" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update
                            </button>
                        </div>


                    </div>
                </div>
            </div>
    </div>
    </form>

    <!-- Hidden Delete Form -->
    <form id="delete-form" action="{{ route('todo.destroy', $todo) }}" method="POST">
        @csrf
        @method('DELETE')
    </form>
    </div>

    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this item?')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>


</x-app-layout>
