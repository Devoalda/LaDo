<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreTodoRequest;
use App\Http\Requests\Project\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
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
    /**
     * Display a listing of all Todos for a Project.
     * This function handles both API and non-API requests
     * API: Returns JSON response of all Todos for a Project paginated
     * Non-API: Returns view of all Todos for a Project
     * @param Request $request - Request object
     * @param $project_id - Project ID of the desired Project
     * @return View|Factory|Application|RedirectResponse|TodoResource|JsonResponse - Returns view of all Todos for a Project or TodoResource/JSON response
     * @throws AuthorizationException - Throws AuthorizationException if user is not authorized to view the Project
     */
    public function index(Request $request, $project_id): Factory|Application|View|RedirectResponse|TodoResource|JsonResponse
    {
        $user = Auth::user();
        $project = $user->projects->find($project_id);

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

        // "Todo index" shows all Todos for all Project
        $todos = $user->projects->map(function ($project) {
            return $project->todos;
        })->flatten();

        return view('todo.index', [
            'todos' => $todos->whereNull('completed_at')->values(),
            'completed' => $todos->whereNotNull('completed_at')->values(),
            'project' => $project,
        ]);
    }

    /**
     * Show the form for creating a new Todo for the particular Project.
     * @param $project_id - Project ID of the desired Project
     * @return View|Factory|Application - Returns view of create Todo form
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
     * @param $project_id - Project ID of the desired Project to store the Todo in
     * @param StoreTodoRequest $request - StoreTodoRequest object with validation rules for Todo creation
     * @return RedirectResponse|JsonResponse - Redirects to Todo index page or returns JSON response
     * @throws AuthorizationException
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
     * Display the specified Todo if API request.
     * Redirects to Todo index page if non-API request. (Shows all Todos for a Project)
     * @param Request $request - Request object (Should contain todo_id if API request)
     * @param $project_id - Project ID of the desired Project
     * @return JsonResponse|RedirectResponse|View - Returns JSON response of Todo if API request or redirects to Todo index page
     * @throws AuthorizationException - Throws AuthorizationException if user is not authorized to view the Todo
     */
    public function show(Request $request, $project_id): JsonResponse|RedirectResponse|View
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
    }

    /**
     * Show the form for editing the specified Todo.
     * @param $project_id - Project ID of todo to be edited
     * @param Todo $todo - Todo to be edited
     * @return View|Factory|Application - Returns view of edit Todo form
     * @throws AuthorizationException - Throws AuthorizationException if user is not authorized to edit the Todo
     */
    public function edit($project_id, Todo $todo): Factory|View|Application
    {
        $projects = auth()->user()->projects;
        $project = $projects->find($project_id);
        $this->authorize('view', [Todo::class, $project, $todo]);

        return view('todo.edit', compact('project', 'todo'));
    }

    /**
     * Update Todo in storage based on the given project
     * @param $project_id - Project ID of the desired Project
     * @param UpdateTodoRequest $request - UpdateTodoRequest object with validation rules for Todo update
     * @param Todo $todo - Todo to be updated
     * @return RedirectResponse|JsonResponse - Redirects to Todo index page or returns JSON response
     */
    public function update($project_id, UpdateTodoRequest $request, Todo $todo): RedirectResponse|JsonResponse
    {
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
     * Remove the specified Todo from storage.
     * @param $project_id - Project ID of the desired Project
     * @param Request $request - Request object
     * @param Todo $todo - Todo to be deleted
     * @return RedirectResponse|JsonResponse - Redirects to Todo index page or returns JSON response
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
