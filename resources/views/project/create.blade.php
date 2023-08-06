<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Project') }}
        </h2>
    </x-slot>

    <!-- Create Project Form (Name, Description) -->
    <div class="py-4">
        <form method="POST" action="{{ route('project.store') }}" id="project-form">
            @csrf
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <div class="text-gray-800 dark:text-gray-100">

                        <div class="mb-4">
                            <label for="name" class="block mb-2 font-semibold">Name</label>
                            <input type="text" name="name" id="name" placeholder="Name"
                                   class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('name') border-red-500 @enderror"
                                   value="{{ old('name') }}">
                            @error('name')
                            <div class="text-red-500 mt-2 text-sm">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block mb-2 font-semibold">Description</label>
                            <textarea name="description" id="description" placeholder="Description"
                                      class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="text-red-500 mt-2 text-sm">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg">
                                Create Project
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>


</x-app-layout>


