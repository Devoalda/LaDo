<div class="py-4" xmlns:livewire="http://www.w3.org/1999/html">
    <form method="POST" action="{{ $editing ? route('pomo.update', $pomo->id) : route('pomo.store') }}" id="pomo-form">
        @csrf
        @if($editing)
            @method('PUT')
        @endif

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="text-gray-800 dark:text-gray-100">

                        <div class="mb-4">
                            <label for="todo_id" class="block mb-2 font-semibold">Todo</label>
                            <select class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('todo_id') border-red-500 @enderror"
                                    name="todo_id" id="todo_id">
                                <option selected value="{{ $editing ? $pomo->todo_id : old('todo_id') }}">{{ $editing ? $pomo->todo->title : 'Select a Todo' }}</option>
                                @foreach($incomplete_todos as $todo)
                                <option value="{{ $todo['id'] }}">{{ $todo['title'] }}</option>
                                @endforeach
                            </select>
                            @error('todo_id')
                            <div class="text-red-500 mt-2 text-sm">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block mb-2 font-semibold">Notes</label>
                            <textarea name="notes" id="notes" placeholder="Notes"
                                      class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('description') border-red-500 @enderror">{{ $editing ? $pomo->notes : old('notes') }}</textarea>
                            @error('notes')
                            <div class="text-red-500 mt-2 text-sm">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                    <div class="mb-4">
                        <!-- pomo_start and pomo_end -->
                        <div class="flex flex-col sm:flex-row sm:space-x-2">
                            <div class="mb-4 sm:mb-0">
                                <label for="pomo_start" class="block mb-2 font-semibold">Start</label>
                                <input type="datetime-local" name="pomo_start" id="pomo_start" placeholder="Start"
                                       class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('pomo_start') border-red-500 @enderror"
                                       value="{{ isset($pomo) ? date('Y-m-d\TH:i', $pomo->pomo_start) : old('pomo_start') }}">
                                @error('pomo_start')
                                <div class="text-red-500 mt-2 text-sm">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="mb-4 sm:mb-0">
                                <label for="pomo_end" class="block mb-2 font-semibold">End</label>
                                <input type="datetime-local" name="pomo_end" id="pomo_end" placeholder="End"
                                       class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 rounded-lg w-full p-4 @error('pomo_end') border-red-500 @enderror"
                                       value="{{ isset($pomo) ? date('Y-m-d\TH:i', $pomo->pomo_end) : old('pomo_end') }}">
                                @error('pomo_end')
                                <div class="text-red-500 mt-2 text-sm">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                    </div>

                        <div class="mt-8">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg">
                                {{ $editing ? 'Update Pomo' : 'Create Pomo' }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
    </form>
</div>
