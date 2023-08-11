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
                ->with('error', 'Project not found');

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
        $user = User::find(auth()->user()->id);
        $this->authorize('create', [Todo::class, $user]);

        $validatedData = $request->validated();

        $project = $user->projects->find($project_id);
        // Add the Todo to the Project
        $project->todos()->save(new Todo($validatedData));

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

        $this->authorize('update', [Todo::class, $project, $todo]);


        return view('todo.edit', compact('project', 'todo'));
    }

    /**
     * Update Todo in storage based on the given project
     */
    public function update($project_id, UpdateTodoRequest $request, Todo $todo)
    {
        $project = auth()->user()->projects->find($project_id);

        $this->authorize('update', [Todo::class, $project, $todo]);

        // Update other fields
        $todo->fill($request->validated());


        $todo->save();

        return back()
            ->with('success', 'Todo updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($project_id, Todo $todo): RedirectResponse
    {
        $this->authorize('delete', [Todo::class, $todo]);

        $todo->delete();

        return redirect()->route('project.todo.index', $project_id)
            ->with('success', 'Todo deleted successfully');
    }
}
