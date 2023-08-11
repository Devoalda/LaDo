<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Response;


class ProjectController extends Controller
{
    /**
     * Display Listing of all Projects.
     */
    public function index(Request $request): Application|Factory|View|JsonResponse
    {
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
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = $request->validated();

        auth()->user()->projects()->create($data);

        return redirect()->route('project.index')
            ->with('info', 'Project created!');
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Project $project): RedirectResponse
    {
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
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $data = $request->validatedWithCompletedAt();

        $project->update($data);

        // Complete all todos in project
        if ($request->has('completed_at') && $request->completed_at) {
            $project->todos()->update(['completed_at' => Carbon::now()->timestamp]);
        }

        return back()->with('info', 'Project updated!');
    }

    /**
     * Remove the specified Project from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('project.index');
    }
}
