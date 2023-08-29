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
     * This function handles both API and non-API requests
     * API: Returns JSON response of all projects
     * Non-API: Returns view of all projects
     * @param Request $request - Request object
     * @return View|Factory|Application|JsonResponse|ProjectResource - Returns view of all projects or ProjectResource/JSON response
     * @throws AuthorizationException
     */
    public function index(Request $request): View|Factory|Application|JsonResponse|ProjectResource
    {
        // Check if API Call, get userID from request
        if ($request->expectsJson()) {
            $user = Auth::user();

            $this->authorize('viewAny', Project::class);
            $projects = $user->projects()->paginate(4);

            return new ProjectResource($projects);
        }

        $user = User::find(auth()->user()->id);
        $projects = $user->projects()->paginate(4);

        // Aggregate all todos for all projects
        $todos = $user->projects->map(function ($project) {
            return $project->todos;
        })->flatten();

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
                ->values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return View|\Illuminate\Foundation\Application|Factory|Application - Returns view of project create page
     */
    public function create(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('project.create');
    }

    /**
     * Store a newly created project in storage.
     * This function handles both API and non-API requests
     * API: Returns JSON response of project if created successfully and 201 status code (created)
     * Non-API: Redirects to project index page if project created successfully
     * @param StoreProjectRequest $request - Request object with validation rules for project creation
     * @return RedirectResponse|JsonResponse - Redirects to project index page or returns JSON response
     */
    public function store(StoreProjectRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        // Check if API Call, get userID from request
        if ($request->expectsJson()) {
            $user = Auth::user();

            $this->authorize('create', Project::class);

            $user->projects()->create($data);

            $data = $user->projects()->latest()->first();

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
     * Display the specified project.
     * This function handles both API and non-API requests
     * API: Returns JSON response of project
     * Non-API: Displays project view of project
     * @param Request $request - Request object
     * @param $project_id - Project ID to be displayed (passed from route)
     * @return RedirectResponse|JsonResponse -
     * Redirects to project index page or returns JSON response
     * @throws AuthorizationException
     */
    public function show(Request $request, $project_id): RedirectResponse|JsonResponse
    {
        // Check if API Call, get userID from request
        if ($request->expectsJson()) {
            $user = Auth::user();

            $project = $user->projects()->find($project_id);

            if (!$project) {
                return response()->json([
                    'error' => 'Project not found',
                ], 404);
            }

            $this->authorize('view', $project);

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
     * @param Project $project - Project object to be edited (passed from route)
     * @return View|\Illuminate\Foundation\Application|Factory|Application - Returns view of project edit page
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
     * This function handles completion toggle and/or updating of other fields
     * @param UpdateProjectRequest $request - Request object with validation rules
     * @param $project_id - Project ID to be updated
     * @return RedirectResponse|JsonResponse - Redirects to previous page or returns JSON response
     * @throws AuthorizationException
     */
    public function update(UpdateProjectRequest $request, $project_id): RedirectResponse|JsonResponse
    {
        $data = $request->validatedWithCompletedAt();

        // API Call
        if ($request->expectsJson()) {
            $user = Auth::user();

            $project = $user->projects()->find($project_id);

            if (!$project) {
                return response()->json([
                    'error' => 'Project not found',
                ], 404);
            }

            $this->authorize('update', $project);

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
     * This function handles both API and non-API requests
     * API: Returns JSON response of project if found and deleted
     * Non-API: Redirects to project index page if project found and deleted
     * @param $project_id - Project ID to be deleted (passed from route)
     * @param Request $request - Request object
     * @return RedirectResponse|JsonResponse - Redirects to project index page or returns JSON response
     * @throws AuthorizationException
     */
    public function destroy($project_id, Request $request): RedirectResponse|JsonResponse
    {
        // Check if API Call and $project_id is provided
        if ($request->expectsJson() && $project_id) {
            $user = Auth::user();

            $project = $user->projects()->find($project_id);

            if (!$project) {
                return response()->json([
                    'error' => 'Project not found',
                    'data' => $request->all(),
                ], 404);
            }

            $this->authorize('delete', $project);

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
