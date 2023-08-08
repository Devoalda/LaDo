<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
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
        $todos = $projects->map(function ($project) {
            return $project->todos;
        })->flatten();

        if ($request->ajax()){
            $view = view('project.load-projects', compact('projects'))->render();
            return Response::json([
                'view' => $view,
                'nextPageUrl' => $projects->nextPageUrl(),
            ]);
        }

        return view('project.index', [
            'projects' => $projects,
            'todos' => $todos->whereNull('completed_at')->values(),
            'completed' => $todos->whereNotNull('completed_at')->values(),
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
        $user = User::find(auth()->user()->id);

        $data = $request->validated();

        $user->projects()->create($data);

        return redirect()->route('project.index')
            ->with('info', 'Project created!');
    }

    /**
     * TODO: Complete this method (if needed)
     * Display the specified resource.
     */
    public function show(Project $project): RedirectResponse
    {
        return redirect()->route('project.index');
    }

    /**
     * TODO: Complete this method (if needed)
     * Show the form for editing the specified Project.
     */
    public function edit(Project $project)
    {
        return view('project.edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified Project in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        $project = $projects->find($project->id);

        $data = $request->validated();

        $project->update($data);

        return back()->with('info', 'Project updated!');
    }

    /**
     * Remove the specified Project from storage.
     */
    public function destroy(Project $project)
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        $project = $projects->find($project->id);

        $project->delete();

        return redirect()->route('project.index');
    }
}
