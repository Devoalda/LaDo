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
