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

       <livewire:dashboard.incomplete-todo/>

        <livewire:dashboard.completed-todo/>

        <!-- Average Todo Count per Project -->
        <livewire:dashboard.ave-todo-per-project/>

        <!-- Project Count -->
        <livewire:dashboard.project-count/>

        <livewire:dashboard.pomo-count/>

        <livewire:dashboard.pomo-time/>
    </div>

</div>
