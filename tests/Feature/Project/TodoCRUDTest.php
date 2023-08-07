<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoCRUDTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Project $project;
    private Todo $todo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($user = User::factory()->create());
        $this->user = $user;
        $this->assertAuthenticated();
        // Create a project through POST and store it in the project property
        $response = $this->post(route('project.store'), [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ]);
        $response->assertRedirect(route('project.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ]);

        $this->assertDatabaseHas('project_user', [
            'project_id' => Project::where('name', 'Test Project')->first()->id,
            'user_id' => $this->user->id,
        ]);

        $this->project = Project::where('name', 'Test Project')->first();

    }

    public function test_user_can_create_todo(): void
    {
        $response = $this->post(route('project.todo.store', $this->project->id), [
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ]);
        $response->assertRedirect(route('project.todo.index', $this->project->id));

        $this->todo = Todo::where('title', 'Test Todo')->first();

        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ]);

        $this->assertDatabaseHas('project_todo', [
            'project_id' => $this->project->id,
            'todo_id' => Todo::where('title', 'Test Todo')->first()->id,
        ]);
    }

    public function test_user_can_view_todo(): void
    {
        $this->test_user_can_create_todo();
        $this->assertAuthenticated();
        $response = $this->get(route('project.index'));
        $response->assertSee('Test Todo');
        $response->assertSee('Test Description');

        $response = $this->get(route('project.todo.index', $this->project->id));
        $response->assertSee('Test Todo');
    }

    public function test_user_can_update_todo(): void
    {
        $this->test_user_can_create_todo();
        $this->assertAuthenticated();
        $response = $this->put(route('project.todo.update', [$this->project->id, $this->todo->id]), [
            'title' => 'Updated Todo',
            'description' => 'Updated Description',
        ]);
        $response->assertRedirect(back()->getTargetUrl());

        $this->assertDatabaseHas('todos', [
            'title' => 'Updated Todo',
            'description' => 'Updated Description',
        ]);

        $this->assertDatabaseMissing('todos', [
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ]);

        $response = $this->get(route('project.todo.index', $this->project->id));
        $response->assertSee('Updated Todo');

    }

    public function test_user_can_delete_todo(): void
    {
        $this->test_user_can_create_todo();
        $this->assertAuthenticated();
        $response = $this->delete(route('project.todo.destroy', [$this->project->id, $this->todo->id]));
        $response->assertRedirect(route('project.todo.index', $this->project->id));

        $this->assertDatabaseMissing('todos', [
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ]);

        $response = $this->get(route('project.todo.index', $this->project->id));
        $response->assertDontSee('Test Todo');
    }

    // Additional Create Tests
    public function test_user_can_create_todo_with_due_start_only(): void
    {
        $this->assertAuthenticated();
        $response = $this->post(route('project.todo.store', $this->project->id), [
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'due_start' => '2020-01-01',
        ]);
        $response->assertRedirect(route('project.todo.index', $this->project->id));

        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'due_start' => '1577836800',
            'due_end' => '1577836800',
        ]);

        $this->assertDatabaseHas('project_todo', [
            'project_id' => $this->project->id,
            'todo_id' => Todo::where('title', 'Test Todo')->first()->id,
        ]);
    }

    public function test_user_can_create_todo_with_due_end_only(): void
    {
        $this->assertAuthenticated();
        $response = $this->post(route('project.todo.store', $this->project->id), [
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'due_end' => '2020-01-01',
        ]);
        $response->assertRedirect(route('project.todo.index', $this->project->id));

        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'due_start' => null,
            'due_end' => '1577836800',
        ]);

        $this->assertDatabaseHas('project_todo', [
            'project_id' => $this->project->id,
            'todo_id' => Todo::where('title', 'Test Todo')->first()->id,
        ]);
    }

    // Additional Update Tests
    public function test_user_can_complete_todo_only(): void
    {
        $this->test_user_can_create_todo();
        $this->assertAuthenticated();
        // Send a PUT request to update route, with "completed_at" set to on
        $response = $this->put(route('project.todo.update', [$this->project->id, $this->todo->id]), [
            'title' => 'Updated Todo',
            'completed_at' => 'on',
        ]);
        $response->assertRedirect(back()->getTargetUrl());

        // Check that the todo is completed in database completed_at value is not null
        $completedTodo = $this->user->projects()->first()->todos()->first();
        $this->assertNotNull($completedTodo->completed_at);
    }

    public function test_user_can_uncomplete_todo(): void
    {
        $this->test_user_can_complete_todo_only();
        $this->assertAuthenticated();
        // Send a PUT request to update route with empty data
        $response = $this->put(route('project.todo.update', [$this->project->id, $this->todo->id]), []);
        $response->assertRedirect(back()->getTargetUrl());

        // Check that the todo is completed in database completed_at value is not null
        $uncompletedTodo = $this->user->projects()->first()->todos()->first();
        $this->assertNull($uncompletedTodo->completed_at);
    }

    // Additional Retrieve Tests (Validation for unauthorized users)
    public function test_other_user_cannot_see_user_todo(): void
    {
        $this->test_user_can_create_todo();
        $this->assertAuthenticated();
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        $response = $this->get(route('project.todo.index', $this->project->id));
        $response->assertDontSee('Test Todo');
    }


}
