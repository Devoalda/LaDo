<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Display Statistics of Todo Completion -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4">
        <h2 class="text-2xl font-semibold mb-4 text-indigo-500 dark:text-indigo-400">
            <!-- Icon for Target board -->
            <svg class="inline-block h-6 w-6 text-indigo-500 dark:text-indigo-400"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 9v6m0 0v6m0-6h6m-6 0H3"/>
            </svg>
            Your Stats
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4 text-red-500 dark:text-red-400">
                    <!-- SVG for Incomplete Todos -->
                    <svg class="inline-block h-6 w-6 text-red-500 dark:text-red-400"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Total incomplete Todos
                </h3>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $incomplete_count }}
                </p>
            </div>

            <!-- Todo Completed Count -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4 text-green-500 dark:text-green-400">
                    <!-- SVG for Completed Todos -->
                    <svg class="inline-block h-6 w-6 text-green-500 dark:text-green-400"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    Total Completed Todos
                </h3>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $todo_completed_count }}
                </p>
            </div>

            <!-- Average Todo Count per Project -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4 text-yellow-500 dark:text-yellow-400">
                    <!-- SVG for Average Todos -->
                    <svg class="inline-block h-6 w-6 text-yellow-500 dark:text-yellow-400"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Average Todos per Project
                </h3>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $ave_todo_count }}
                </p>
            </div>

            <!-- Project Count -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4 text-blue-500 dark:text-blue-400">
                    <!-- SVG for Project Count -->
                    <svg class="inline-block h-6 w-6 text-blue-500 dark:text-blue-400"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Total Projects
                </h3>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $project_count }}
                </p>
            </div>

            <livewire:dashboard.pomo-count/>

            <livewire:dashboard.pomo-time/>
        </div>

    </div>


    <!-- List out Todos and their details (time left/ago) + checkbox in form to toggle completed -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4 py-3">
        <h2 class="text-2xl font-semibold mb-4 text-green-500 dark:text-green-400">
            Today's Todos
            ({{ $incomplete_count }})
        </h2>
        <div class="space-y-4" id="todo-container">
            @include('dashboard.load-todo')
        </div>
        <!-- Pagination with CSS -->
        <div class="invisible">
            {{ $todos->links() }}
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let nextPageUrl = '{{ $todos->nextPageUrl() }}';

            $(window).scroll(function () {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    if (nextPageUrl) {
                        loadMoreTodos();
                    }
                }
            });

            function loadMoreTodos() {
                $.ajax({
                    url: nextPageUrl,
                    type: 'get',
                    beforeSend: function () {
                        nextPageUrl = '';
                    },
                    success: function (data) {
                        nextPageUrl = data.nextPageUrl;
                        $('#todo-container').append(data.view);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error loading more posts:", error);
                    }
                });
            }
        });
    </script>

</x-app-layout>
