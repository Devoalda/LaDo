<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreTodoRequest;
use App\Http\Requests\Project\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectTodoController extends Controller
{
    private string $timezone = 'Asia/Singapore';

    /**
     * Display a listing of all Todos for a Project.
     */
    public function index(Request $request, $project_id): Factory|Application|View|RedirectResponse|TodoResource|JsonResponse
    {
        $user = Auth::user();
        $project = $user->projects()->find($project_id);

        if (!$project) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Project not found',
                ], 404);
            }
            return redirect()->route('project.index')
                ->with('error', 'Project not found');
        }

        $this->authorize('view', $project);

        if ($request->expectsJson()) {
            $this->authorize('viewAny', $project);

            $todos = $project->todos()->paginate(4);

            return new TodoResource($todos);
        }

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
        $project = Auth::user()->projects->find($project_id);
        return view('todo.create', [
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created Todo in storage.
     */
    public function store($project_id, StoreTodoRequest $request): RedirectResponse|JsonResponse
    {
        $validatedData = $request->validated();
        $user = Auth::user();
        $project = $user->projects->find($project_id);

        $this->authorize('create', [Todo::class, $user]);

        // Add the Todo to the Project
        $project->todos()->save(new Todo($validatedData));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Todo created successfully.',
                'data' => $project->todos()->latest()->first(),
            ], 201);
        }

        return redirect()->route('project.todo.index', $project_id)
            ->with('success', 'Todo created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $project_id)
    {
        if ($request->expectsJson()) {
            $user = Auth::user();
            $project = $user->projects->find($project_id);
            $todo = $project->todos->find($request->todo_id);

            $this->authorize('view', [Todo::class, $project, $todo]);

            return response()->json([
                'message' => 'Todo retrieved successfully.',
                'data' => $todo,
            ], 200);
        }

        return redirect()->route('project.todo.index', $project_id);
//        $user = Auth::user();
//        $projects = $user->projects;
//        $project = $projects->find($project_id);
//
//        $this->authorize('view', [Todo::class, $project, $project->todos->find($request->todo_id)]);
//
//        return view('todo.show', compact('project', 'todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($project_id, Todo $todo)
    {
        $projects = auth()->user()->projects;
        $project = $projects->find($project_id);
        $this->authorize('view', [Todo::class, $project, $todo]);

        return view('todo.edit', compact('project', 'todo'));
    }

    /**
     * Update Todo in storage based on the given project
     */
    public function update($project_id, UpdateTodoRequest $request, Todo $todo)
    {
//        $project = auth()->user()->projects->find($project_id);

        if (Gate::denies('update', $todo)) {
            return back()->with('error', 'You are not authorized to update this todo');
        }

        // Update other fields
        $todo->fill($request->validated());

        $dueStart = $request->due_start ? strtotime(Carbon::parse($request->due_start)) : null;
        $dueEnd = $request->due_end ? strtotime(Carbon::parse($request->due_end)) : null;

        if ($dueEnd === null && $dueStart !== null) {
            $dueEnd = strtotime(Carbon::parse($todo->due_start));
        }

        $todo->due_start = $dueStart;
        $todo->due_end = $dueEnd;

        $todo->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Todo updated successfully',
                'data' => $todo,
            ], 200);
        }

        return back()->with('success', 'Todo updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($project_id, Request $request, Todo $todo): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', [Todo::class, $todo]);

        $todo->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Todo deleted successfully',
            ], 200);
        }

        return redirect()->route('project.todo.index', $project_id)
            ->with('success', 'Todo deleted successfully');
    }
}
