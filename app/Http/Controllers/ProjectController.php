<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;


class ProjectController extends Controller
{
    /**
     * Display Listing of all Projects.
     */
    public function index(Request $request): View|Factory|Application|JsonResponse|ProjectResource
    {
        // Check if API Call, get userID from request
        if ($request->is('api/*')) {
            $user = Auth::user();

            $projects = $user->projects()->paginate(4);
            return new ProjectResource($projects);
        }

        $user = User::find(auth()->user()->id);
        $projects = $user->projects()->paginate(4);
        // Aggregate all todos for all projects

        $todos = $user->todos()
            ->map(function ($todo) {
                return Todo::find($todo->id);
            });

        if ($request->ajax()) {
            $view = view('project.load-projects', compact('projects'))->render();
            return Response::json([
                'view' => $view,
                'nextPageUrl' => $projects->nextPageUrl(),
            ]);
        }

        return view('project.index', [
            'projects' => $projects,
            'todos' => $todos->whereNull('completed_at')->values(),
            'completed' => $todos->whereNotNull('completed_at')
                ->whereBetween('completed_at', [strtotime('today midnight'), strtotime('today midnight + 1 day')])
                ->values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('project.create');
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        // Check if API Call, get userID from request
        if ($request->is('api/*')) {
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $user->projects()->create($data);

            return response()->json([
                'message' => 'Project created successfully',
                'data' => $data,
            ], 201);
        }

        auth()->user()->projects()->create($data);

        return redirect()->route('project.index')
            ->with('info', 'Project created!');
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Request $request, $project_id): RedirectResponse|JsonResponse
    {
        // Check if API Call, get userID from request
        if ($request->is('api/*')) {
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $project = $user->projects()->find($project_id);

            if (!$project) {
                return response()->json([
                    'error' => 'Project not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Project retrieved successfully',
                'data' => $project,
            ], 200);
        }

        // Non-API request handling
        $project = Project::findOrFail($project_id); // Assumes the Project model is imported

        $this->authorize('view', $project);

        return redirect()->route('project.index');
    }

    /**
     * Show the form for editing the specified Project.
     * @throws AuthorizationException
     */
    public function edit(Project $project): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $this->authorize('view', $project);
        return view('project.edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified Project in storage.
     */
    public function update(UpdateProjectRequest $request, $project_id): RedirectResponse|JsonResponse
    {

        $data = $request->validatedWithCompletedAt();

        // API Call
        if ($request->is('api/*')) {
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $project = $user->projects()->find($project_id);

            if (!$project) {
                return response()->json([
                    'error' => 'Project not found',
                ], 404);
            }

            $project->update($data);

            return response()->json([
                'message' => 'Project updated successfully',
                'data' => $project,
            ], 200);
        }

        $project = Project::find($project_id);

        if (!$project) {
            return response()->json([
                'error' => 'Project not found',
            ], 404);
        }

        $this->authorize('update', $project);

        $project->update($data);

        // Complete all todos in project
        if ($request->has('completed_at') && $request->completed_at) {
            $project->todos()->update(['completed_at' => Carbon::now()->timestamp]);
        }

        return back()->with('info', 'Project updated!');
    }

    /**
     * Remove the specified Project from storage.
     * @throws AuthorizationException
     */
    public function destroy($project_id, Request $request): RedirectResponse|JsonResponse
    {
        // Check if API Call and $project_id is provided
        if ($request->is('api/*')) {
            $user_id = $request->user_id;
            $user = User::find($user_id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $project = $user->projects()->find($project_id);

            if (!$project) {
                return response()->json([
                    'error' => 'Project not found',
                    'data' => $request->all(),
                ], 404);
            }

            $project->delete();

            return response()->json([
                'message' => 'Project deleted successfully',
            ], 200);
        }

        // Non-API request handling
        $project = Project::findOrFail($project_id);

        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('project.index');
    }

}
