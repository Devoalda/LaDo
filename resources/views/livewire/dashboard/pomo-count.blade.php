<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4 text-blue-500 dark:text-blue-400">
                    <!-- SVG for Pomo Average Count -->
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
                    Average Pomos per Project
                </h3>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $ave_pomo_count }}
                </p>
            </div>
