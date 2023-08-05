<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Todo') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <form method="POST" action="{{ route('todo.update', $todo) }}">
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
                                       value="{{ old('due_start', $todo->due_start) }}">
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
                                       value="{{ old('due_end', $todo->due_end) }}">
                                @error('due_end')
                                <div class="text-red-900 mt-2 text-sm">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end mt-4">
                            <!-- Cancel Button (GET request to index route) -->
                            <button type="button"
                                    class="bg-blue-500 hover:bg-blue-600 text-gray-800 dark:text-white-700 dark:bg-blue-100 py-2 px-4 rounded mr-2"
                                    onclick="window.location.href='{{ route('todo.index') }}'">
                                Cancel
                            </button>
                            <!-- Update Button -->
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-800 text-grey-800 dark:text-white-700 dark:bg-green-800 py-2 px-4 rounded">
                                Update
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>


</x-app-layout>
