<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        // Aggregate all todos for all projects
        $todos = $projects->map(function ($project) {
            return $project->todos;
        })->flatten();

        return view('project.index', [
            'projects' => $projects,
            'todos' => $todos->whereNull('completed_at')->values(),
            'completed' => $todos->whereNotNull('completed_at')->values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('project.create');
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $data = $request->validate([
            'name' => 'required|unique:projects|max:255',
            'description' => 'nullable|max:255',
        ]);

        $user->projects()->save(new Project($data));

        return redirect()->route('project.index');
    }

    /**
     * TODO: Complete this method (if needed)
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return redirect()->route('project.index');
    }

    /**
     * TODO: Complete this method (if needed)
     * Show the form for editing the specified Project.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified Project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $user = User::find(auth()->user()->id);
        $projects = $user->projects;
        $project = $projects->find($project->id);

        $data = $request->validate([
            'name' => 'required|unique:projects|max:255',
            'description' => 'nullable|max:255',
        ]);

        $project->update($data);

        return back()->with('status', 'Project updated!');
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
