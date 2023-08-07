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

    @include('todo.todo_list')

</x-app-layout>
