<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCRUDTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($user = User::factory()->create());
        $this->user = $user;
        $this->assertAuthenticated();

    }

    public function test_user_can_create_project(): void
    {
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

    // View Project after creation (Show to be implemented)
    public function test_user_can_view_project(): void
    {
        $this->test_user_can_create_project();
        // Get from project index page and assert that the project is visible
        $response = $this->get(route('project.index'));
        // Search for the project name and description
        $response->assertSee('Test Project');
        $response->assertSee('Test Description');
    }

    // Edit Project after creation
    public function test_user_can_edit_project(): void
    {
        $this->test_user_can_create_project();
        $response = $this->get(route('project.edit', $this->project->id));
        $response->assertOk();
        $response->assertSee('Test Project');
        $response->assertSee('Test Description');
    }

    // Update Project after creation
    public function test_user_can_update_project(): void
    {
        $this->test_user_can_create_project();
        $response = $this->put(route('project.update', $this->project->id), [
            'name' => 'Test Project Updated',
            'description' => 'Test Description Updated',
        ]);
        $response->assertRedirect(back()->getTargetUrl());
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project Updated',
            'description' => 'Test Description Updated',
        ]);
    }

    // Delete Project after creation
    public function test_user_can_delete_project(): void
    {
        $this->test_user_can_create_project();
        $response = $this->delete(route('project.destroy', $this->project->id));
        $response->assertRedirect(route('project.index'));
        $this->assertDatabaseMissing('projects', [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ]);
    }

    // View Project after deletion
    public function test_user_cannot_view_deleted_project(): void
    {
        $this->test_user_can_delete_project();
        $response = $this->get(route('project.show', $this->project->id));
        $response->assertNotFound();
    }

    // Edit Project after deletion
    public function test_user_cannot_edit_deleted_project(): void
    {
        $this->test_user_can_delete_project();
        $response = $this->get(route('project.edit', $this->project->id));
        $response->assertNotFound();
    }

    // Update Project after deletion
    public function test_user_cannot_update_deleted_project(): void
    {
        $this->test_user_can_delete_project();
        $response = $this->put(route('project.update', $this->project->id), [
            'name' => 'Test Project Updated',
            'description' => 'Test Description Updated',
        ]);
        $response->assertNotFound();
    }

    // Delete Project after deletion
    public function test_user_cannot_delete_deleted_project(): void
    {
        $this->test_user_can_delete_project();
        $response = $this->delete(route('project.destroy', $this->project->id));
        $response->assertNotFound();
    }

}
