<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected array $user = [
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => 'password1234'
    ];

    private string $project_route = '/api/project';
    private $access_token;
    private $user_id;
    private $project_id;

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

    public function test_user_can_view_all_projects(): void
    {
        $response = $this->getJson($this->project_route);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getProjectJsonStructure(),
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
            ]);
    }

    public function test_user_can_create_project(): void
    {
        $response = $this->postJson($this->project_route, [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ]);


        $response->assertCreated()
            ->assertJsonStructure([
                'data' => $this->getProjectJsonStructure(),
            ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ]);

        $this->assertDatabaseHas('project_user', [
            'user_id' => $this->user_id,
            'project_id' => $response['data']['id'],
        ]);

        $this->project_id = $response['data']['id'];
    }

    public function test_user_can_get_created_project_from_api(): void
    {
        $this->test_user_can_create_project();

        $response = $this->getJson($this->project_route . '/' . $this->project_id);

        $response->assertOk();
    }

    public function test_user_can_delete_created_project_from_api(): void
    {
        $this->test_user_can_create_project();

        $response = $this->deleteJson($this->project_route . '/' . $this->project_id);

        $response->assertOk();
    }

    protected function getProjectJsonStructure()
    {
        return [
            'id',
            'name',
            'description',
            'created_at',
            'updated_at',
        ];
    }


}
