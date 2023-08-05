<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|Application
    {
        // Get all todos from database (todos table), where user_id is the same as the authenticated user
        $todos = Todo::where('user_id', auth()->user()->id)
            // Show ALL not completed (Null or empty)
            ->whereNull('completed_at')
            ->orWhere('completed_at', '')
            // And all completed today
            ->orWhereDate('completed_at', today())
            ->orderByDesc('created_at')
            ->get([
                'id',
                'title',
                'description',
                'completed_at',
                'due_start',
                'due_end'
            ]);

        return view('todo.index', compact('todos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Factory|View|Application
    {
        return view('todo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:255',
            'due_start' => 'nullable|date',
            // due_end is not required, but if it is provided, it must be after due_start
            'due_end' => 'nullable|after:due_start',
        ]);

        $validatedData = array_merge($validatedData, [
            'due_end' => $request->due_end ?? $request->due_start,
            'user_id' => auth()->user()->id,
        ]);

        Todo::create($validatedData);

        return redirect()->route('todo.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo): Factory|View|Application
    {
        $todo = Todo::where('user_id', auth()->user()->id)
            ->where('id', $todo->id)
            ->firstOrFail();
        return view('todo.show', compact('todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo): Factory|View|Application
    {
        $todo = Todo::where('user_id', auth()->user()->id)
            ->where('id', $todo->id)
            ->firstOrFail();
        return view('todo.edit', compact('todo'));
    }

    /**
     * Update the specified resource in storage.
     * Can either update completed_at or All fields
     */
    public function update(UpdateTodoRequest $request, Todo $todo): RedirectResponse
    {
        // Get Data
        $data = $request->post();

        // Check if the request contains only token and method
        $emptyPost = count($data) === 2;

        if ($emptyPost) {
            // If empty post, toggle completed_at
            $todo->completed_at = $todo->completed_at ? null : now();
        } elseif ((isset($data['completed_at']) && $data['completed_at'] === 'on')) {
            // If completed_at=on, set completed_at to now()
            $todo->completed_at = now();
        } else {
            // Validate Data
            $data = $request->validate([
                'title' => 'nullable|max:255',
                'description' => 'nullable|max:255',
                'due_start' => 'nullable|date',
                'due_end' => 'nullable|after:due_start',
            ]);
        }

        // Update todo
        $todo->update($data);

        return redirect()->route('todo.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo): RedirectResponse
    {
        $todo = Todo::where('user_id', auth()->user()->id)
            ->where('id', $todo->id)
            ->firstOrFail();
        $todo->delete();

        return redirect()->route('todo.index');
    }
}
