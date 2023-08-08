<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Project List') }}
            <a href="{{ route('project.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded float-right">
                Create Project
            </a>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="project-container">
                @include('project.load-projects')
            </div>
        </div>
    </div>

    <div class="invisible">
        {{ $projects->links() }}
    </div>

    <script>
        const deleteButtons = document.querySelectorAll('.delete-button');
        const cancelButtons = document.querySelectorAll('.cancel-button');
        const modals = document.querySelectorAll('.modal');

        deleteButtons.forEach((deleteButton, index) => {
            deleteButton.addEventListener('click', () => {
                modals[index].classList.remove('hidden');
            });

            cancelButtons[index].addEventListener('click', () => {
                modals[index].classList.add('hidden');
            });
        });

        $(document).ready(function () {
            let nextPageUrl = '{{ $projects->nextPageUrl() }}';

            $(window).scroll(function () {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 2000) {
                    if (nextPageUrl) {
                        loadMoreProjects();
                    }
                }
            });

            function loadMoreProjects() {
                $.ajax({
                    url: nextPageUrl,
                    type: 'get',
                    beforeSend: function () {
                        nextPageUrl = '';
                    },
                    success: function (data) {
                        nextPageUrl = data.nextPageUrl;
                        $('#project-container').append(data.view);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error loading more posts:", error);
                    }
                });
            }
        });
    </script>

    @include('todo.todo_list')


</x-app-layout>
