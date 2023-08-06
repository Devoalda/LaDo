<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Carbon;

class TodoController extends Controller
{
    private string $timezone = "Asia/Singapore";

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Application
    {
        $user = User::find(auth()->user()->id);

        $projects = $user->projects;

        $allTodos = [];
        $allProjects = [];

        foreach ($projects as $project) {
            $todos = $project->todos;
        }

        return view('todo.index', [
            'todos' => $todos->whereNull('completed_at')->values(),
            'completed' => $todos->whereNotNull('completed_at')->values(),
            'projects' => $projects,
        ]);
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

        $due_end = match (true) {
            isset($validatedData['due_end']) => strtotime($validatedData['due_end']),
            isset($validatedData['due_start']) => strtotime($validatedData['due_start']),
            default => null,
        };

        $validatedData = array_merge($validatedData, [
            // due_end = due_start if due_end is not provided and due_start is provided or null
            'due_end' => $due_end,
            'user_id' => auth()->user()->id,
        ]);

        // Modify all dates to unix timestamp
        if (isset($validatedData['due_start']))
            $validatedData['due_start'] = strtotime($validatedData['due_start']);
        if (isset($validatedData['due_end']))
            $validatedData['due_end'] = strtotime($validatedData['due_end']);

        $todo = new Todo($validatedData);


        $user = User::find(auth()->user()->id);
        $project = $user->projects->first();
        $project->todo()->save($todo);

        // Set flash message
        session()->flash('success', 'Todo created successfully.');

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
     * Can either update completed_at (Checkbox) or other fields
     *
     * @param UpdateTodoRequest $request UpdateTodoRequest
     * @param Todo $todo Todo Object
     * @return RedirectResponse Redirect to todo.index
     */
    public function update(UpdateTodoRequest $request, Todo $todo): RedirectResponse
    {
        $data = $request->only(['title', 'description', 'due_start', 'due_end', 'completed_at']);

        if (Request::filled('completed_at')) {
            $todo->completed_at = Request::input('completed_at') === 'on' ? strtotime(now($this->timezone)) : null;
            $todo->save();
            return redirect()->route('todo.index')->with('success', 'Good job! Todo completed.');
        } else {
            // If 'completed_at' is not provided, toggle its value (only if the request is empty)
            if (empty($data))
                $todo->completed_at = $todo->completed_at ? null : strtotime(now($this->timezone));
            else
                // Continue to update other fields
                unset($data['completed_at']);
        }

        if (Request::filled('due_start')) {
            $data['due_start'] = strtotime(Request::input('due_start'));
        }

        if (Request::filled('due_end')) {
            $data['due_end'] = strtotime(Request::input('due_end'));
        } elseif (isset($data['due_start'])) {
            // If 'due_end' is not provided, set it to 'due_start' value
            $data['due_end'] = $data['due_start'];
        }

        $todo->update($data);

        return redirect()->route('todo.index')->with('success', 'Todo updated successfully.');
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

        // Set flash message
        session()->flash('success', 'Todo deleted successfully.');

        return redirect()->route('todo.index');
    }
}
