<?php

namespace App\Http\Controllers;

//use App\Http\Requests\StoreTodoRequest;
//use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Carbon;

class ProjectTodoController extends Controller
{
    private string $timezone = 'Asia/Singapore';
    /**
     * Display a listing of all Todos for a Project.
     */
    public function index($project_id)
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        $project = $projects->find($project_id);

        $todos = $project->todos;

        return view('project.todo', [
            'todos' => $todos->whereNull('completed_at')->values(),
            'completed' => $todos->whereNotNull('completed_at')->values(),
            'project' => $project,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Factory|View|Application
    {
        return view('project.create');
    }

    /**
     * Store a newly created Todo in storage.
     */
    public function store($project_id, Request $request)
    {
        $validatedData = Request::validate([
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
        $project = $user->projects->find($project_id);
        $project->todos()->save($todo);

        return redirect()->route('project.todo.index', $project_id)
            ->with('success', 'Todo created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($project_id, Todo $todo)
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        $project = $projects->find($project_id);

        return view('project.todo.show', compact('project', 'todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($project_id, Todo $todo)
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        $project = $projects->find($project_id);

        return view('project.edit', compact('project', 'todo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($project_id, Request $request, Todo $todo)
    {
        $data = Request::only(['title', 'description', 'due_start', 'due_end', 'completed_at']);

        if (Request::filled('completed_at')) {
            $todo->completed_at = Request::input('completed_at') === 'on' ? strtotime(now($this->timezone)) : null;
            $todo->save();
            return back()->with('success', 'Todo updated successfully');
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
            $data['due_end'] = strtotime(Request::input('due_start'));
        }

        $todo->update($data);

        return redirect()->route('project.todo.index', $project_id)
            ->with('success', 'Todo updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($project_id, Todo $todo)
    {
        $todo->delete();

        return redirect()->route('project.todo.index', $project_id)
            ->with('success', 'Todo deleted successfully');
    }
}
