<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mt-4 py-3">
    <div class="flex justify-center items-center space-x-4">
        <button
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
            wire:click="startTimer">
            Start
        </button>
        <button
            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded"
            wire:click="stopTimer">
            Stop
        </button>
    </div>

    <div class="flex justify-center items-center mt-8">
        <div class="w-1/2">
            <div class="relative pt-1">
                <!-- Default max is 25 minutes, progress bar 25 minutes = 100%, warning indicator to change color at different percentages -->
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded
                    @if($time >= 3600) bg-green-200 @elseif($time >= 60) bg-yellow-200 @else bg-red-200 @endif">
                    <div
                    style="width:@if($time >= 3600) 100% @elseif($time >= 60) {{ $time / 60 * 100 }}% @else {{ $time / 60 * 100 }}% @endif"
                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center
                            @if($time >= 3600) bg-green-500 @elseif($time >= 60) bg-yellow-500 @else bg-red-500 @endif">
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="text-4xl font-bold text-center mt-4"
         @if($countdown)
            wire:poll.1s="tick"
        @endif
         id="timer">
        @if($time >= 3600)
            {{ gmdate('H:i:s', $time) }}
        @elseif($time >= 60)
            {{ gmdate('i:s', $time) }}
        @else
            {{ gmdate('s', $time) }} Seconds
        @endif
    </div>

    @if($break)
    <div id="break" class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-50 z-10">
        <div class="flex justify-center items-center w-full h-full">
            <div class="text-center">
                <h1 class="text-5xl text-white font-bold">Time's up!</h1>
                <h2 class="text-3xl text-white font-semibold">Take a break</h2>
                <button wire:click="endBreak(5)"
                        class="mt-4 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Reset
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
