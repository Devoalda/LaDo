<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreTodoRequest;
use App\Http\Requests\Project\UpdateTodoRequest;
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
    public function index($project_id): Factory|Application|View|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        $project = $projects->find($project_id);

        if (!$project || $project->user->id !== auth()->user()->id)
            return back()
                ->with('error', 'Project not found')
                ->setStatusCode(404);

        $todos = $project->todos;

        return view('todo.index', [
            'todos' => $todos->whereNull('completed_at')->values(),
            'completed' => $todos->whereNotNull('completed_at')->values(),
            'project' => $project,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($project_id): Factory|View|Application
    {
        $project = auth()->user()->projects->find($project_id);
        return view('todo.create', [
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created Todo in storage.
     */
    public function store($project_id, StoreTodoRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        if(isset($validatedData['due_start']))
            $validatedData['due_start'] = strtotime($validatedData['due_start']);

        if (isset($validatedData['due_end']))
            $validatedData['due_end'] = strtotime($validatedData['due_end']);
        elseif (isset($validatedData['due_start']))
            $validatedData['due_end'] = $validatedData['due_start'];
//
        $todo = new Todo($validatedData);

        $user = User::find(auth()->user()->id);
        $project = $user->projects->find($project_id);
        // Add the Todo to the Project
        $project->todos()->save($todo);

        return redirect()->route('project.todo.index', $project_id)
            ->with('success', 'Todo created successfully.');
//            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($project_id, Todo $todo)
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        $project = $projects->find($project_id);

        if (!$project || $project->user->id !== auth()->user()->id || $todo->user()[0]->id !== auth()->user()->id)
            return back()->with('error', 'Project/Todo not found');

        return view('todo.show', compact('project', 'todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($project_id, Todo $todo)
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        $project = $projects->find($project_id);

        if (!$project || $project->user->id !== auth()->user()->id || $todo->user()[0]->id !== auth()->user()->id)
            return back()->with('error', 'Project/Todo not found');

        // Check if the given todo is in the given project (Reverse find with todo's project_id)
        if ($todo->project->id !== $project_id)
            return back()->with('error', 'Todo not found in the given project');

        return view('todo.edit', compact('project', 'todo'));
    }

    /**
     * Update Todo in storage based on the given project
     */
    public function update($project_id, Request $request, Todo $todo)
    {
        if ($todo->project->id !== $project_id) {
            return back()->with('error', 'Todo not found in the given project');
        }

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

        return back()
            ->with('success', 'Todo updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($project_id, Todo $todo): RedirectResponse
    {
        $todo->delete();

        return redirect()->route('project.todo.index', $project_id)
            ->with('success', 'Todo deleted successfully');
    }
}
