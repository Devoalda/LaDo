<?php

namespace Tests\Feature\API;

use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    protected array $user = [
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => 'password1234'
    ];

    private string $project_route = '/api/project/';
    private $access_token;
    private $user_id;
    private $project_id;
    private $todo_id;

    protected function setUp(): void
    {
        parent::setUp();

        // Register user
        $this->registerUser();

        // Authenticate user
        $this->authenticateUser();
    }

    protected function registerUser(): void
    {
        $response = $this->postJson('/api/register', $this->user);
        $response->assertCreated();
    }

    protected function authenticateUser(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user['email'],
            'password' => $this->user['password'],
        ]);
        $response->assertOk();

        $this->access_token = $response['access_token'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->access_token,
        ])->getJson('/api/me');
        $response->assertOk();

        $this->user_id = $response['id'];
    }

    public function create_project_and_todo(): void
    {
        $this->actingAs(User::find($this->user_id));

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
            'user_id' => $this->user_id,
        ]);

        $this->project_id = Project::where('name', 'Test Project')->first()->id;

        $response = $this->post(route('project.todo.store', $this->project_id), [
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ]);

        $response->assertRedirect(route('project.todo.index', $this->project_id));

        $this->todo_id = Todo::where('title', 'Test Todo')->first()->id;

        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ]);

        $this->assertDatabaseHas('project_todo', [
            'project_id' => $this->project_id,
            'todo_id' => $this->todo_id,
        ]);

    }

    public function test_user_can_see_todo_of_project(): void
    {
        $this->create_project_and_todo();

        $response = $this->getJson($this->project_route . $this->project_id . '/todo');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
            ]);
    }

    public function test_user_can_create_todo(): void
    {
        $this->create_project_and_todo();

        $response = $this->postJson($this->project_route . $this->project_id . '/todo', [
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ]);

        $this->assertDatabaseHas('project_todo', [
            'project_id' => $this->project_id,
            'todo_id' => $response['data']['id'],
        ]);
    }

    public function test_user_can_edit_todo(): void
    {
        $this->create_project_and_todo();

        $response = $this->putJson($this->project_route . $this->project_id . '/todo/' . $this->todo_id, [
            'title' => 'Updated Todo',
            'description' => 'Updated Description',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_complete_todo()
    {
        $this->create_project_and_todo();

        $response = $this->putJson($this->project_route . $this->project_id . '/todo/' . $this->todo_id,
            [
                'completed_at' => 'on',
            ]
        );

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'completed_at',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_can_uncomplete_todo(): void
    {
        $this->create_project_and_todo();

        $response = $this->putJson($this->project_route . $this->project_id . '/todo/' . $this->todo_id,
            [
                'completed_at' => 'on',
            ]
        );

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'completed_at',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $response = $this->putJson($this->project_route . $this->project_id . '/todo/' . $this->todo_id,
            []
        );

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'completed_at',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Check database for "completed_at" is null
        $this->assertDatabaseHas('todos', [
            'id' => $this->todo_id,
            'completed_at' => null,
        ]);
    }

    public function test_user_can_delete_todo(): void
    {
        $this->create_project_and_todo();

        $response = $this->deleteJson($this->project_route . $this->project_id . '/todo/' . $this->todo_id);

        $response->assertOk();
    }
}
