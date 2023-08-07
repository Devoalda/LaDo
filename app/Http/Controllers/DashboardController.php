<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = $this->all_incomplete_todos();

        $todos = $data['todos'];
        $incomplete_count = $data['incomplete_count'];
        // Convert all todo to Todo model
        $todos->transform(function ($todo) {
            return \App\Models\Todo::find($todo->id);
        });

        $projects = $this->projects();
        $project_count = $projects['project_count'];
        $ave_todo_count = $projects['ave_todo_count'];

        $todo_completed_count = $this->all_completed_todos()['completed_count'];

        return view('dashboard', compact(
            'todos',
            'incomplete_count',
            'project_count',
            'ave_todo_count',
            'todo_completed_count'
        ));
    }

    public function all_incomplete_todos(): array
    {
        $user = auth()->user();
        // Join project_todo first and project_user tables, paginate the results
        $todos = DB::table('todos')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('project_user', 'project_todo.project_id', '=', 'project_user.project_id')
            ->where('project_user.user_id', '=', $user->id)
            ->whereDate('due_end', '<=', strtotime('today midnight'))
            ->whereNull('completed_at')
            ->orderBy('due_end', 'asc')
            ->paginate(5);

        $all_incomplete_count = DB::table('todos')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('project_user', 'project_todo.project_id', '=', 'project_user.project_id')
            ->where('project_user.user_id', '=', $user->id)
            ->whereDate('due_end', '<=', strtotime('today midnight'))
            ->whereNull('completed_at')
            ->count();

        $all_incomplete_count = max($all_incomplete_count, 0);

        return [
            'todos' => $todos,
            'incomplete_count' => $all_incomplete_count,
        ];
    }

    public function projects(): array
    {
        $user = auth()->user();
        $projects = $user->projects;
        $project_count = $projects->count();

        // Average number of todos per project
        $ave_todo_count = function ($projects) {
            $todo_count = 0;
            foreach ($projects as $project) {
                $todo_count += $project->todos->count();
            }
            return $todo_count / $projects->count();
        };

        return [
            'projects' => $projects,
            'project_count' => $project_count,
            'ave_todo_count' => $ave_todo_count($projects),
        ];
    }

    public function all_completed_todos(): array
    {
        $user = auth()->user();
        // Join project_todo first and project_user tables, paginate the results
        $todos = DB::table('todos')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('project_user', 'project_todo.project_id', '=', 'project_user.project_id')
            ->where('project_user.user_id', '=', $user->id)
            ->whereDate('due_end', '<=', strtotime('today midnight'))
            ->whereNotNull('completed_at')
            ->paginate(5);

        $all_completed_count = DB::table('todos')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('project_user', 'project_todo.project_id', '=', 'project_user.project_id')
            ->where('project_user.user_id', '=', $user->id)
            ->whereDate('due_end', '<=', strtotime('today midnight'))
            ->whereNotNull('completed_at')
            ->count();

        $all_completed_count = max($all_completed_count, 0);

        return [
            'todos' => $todos,
            'completed_count' => $all_completed_count,
        ];
    }
}
