<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PomoTest extends TestCase
{
    use RefreshDatabase;

    protected array $user = [
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => 'password1234'
    ];

    private string $pomo_route = '/api/pomo/';
    private $access_token;
    private $user_id;
    private $project_id;
    private $todo_id;

    private $pomo_id;

    protected function setUp(): void
    {
        parent::setUp();

        // Register user
        $this->registerUser();

        // Authenticate user
        $this->authenticateUser();

        // Create Project
        $this->createProject();

        // Create Todo
        $this->createTodo();

        // Create Pomo
        $this->createPomo();
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

    protected function createProject(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->access_token,
        ])->postJson('/api/project', [
            'name' => 'Test Project',
            'description' => 'Test Project Description',
        ]);
        $response->assertCreated();

        $this->project_id = $response['data']['id'];
    }

    private function createTodo()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->access_token,
        ])->postJson('/api/project/' . $this->project_id . '/todo', [
            'title' => 'Test Todo',
            'description' => 'Test Todo Description',
        ]);
        $response->assertCreated();

        $this->todo_id = $response['data']['id'];
    }

    private function createPomo()
    {
        $start = date('Y-m-d\TH:i');
        $end = date('Y-m-d\TH:i', strtotime('+25 minutes'));

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->access_token,
        ])->postJson($this->pomo_route, [
            'todo_id' => $this->todo_id,
            'pomo_start' => $start,
            'pomo_end' => $end,
            'notes' => 'Test Pomo Notes',
        ]);
        $response->assertCreated();

        $this->pomo_id = $response['data']['id'];
    }

    public function test_user_can_view_all_pomo(): void
    {
        $response = $this->getJson($this->pomo_route . '?todo_id=' . $this->todo_id);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getPomoJsonStructure(),
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
            ]);
    }

    protected function getPomoJsonStructure(): array
    {
        return [
            'id',
            'todo_id',
            'pomo_start',
            'pomo_end',
            'notes',
            'created_at',
            'updated_at',
        ];
    }

    public function test_user_can_view_created_pomo(): void
    {
        $response = $this->getJson($this->pomo_route . $this->pomo_id);
        $response->assertOk()
            ->assertJsonStructure([
                'data' => $this->getPomoJsonStructure(),
            ]);
    }

    public function test_user_can_edit_pomo(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->access_token,
        ])->putJson($this->pomo_route . $this->pomo_id, [
            'todo_id' => $this->todo_id,
            'pomo_start' => date('Y-m-d\TH:i'),
            'pomo_end' => date('Y-m-d\TH:i', strtotime('+25 minutes')),
            'notes' => 'Updated Pomo Notes',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => $this->getPomoJsonStructure(),
            ]);

        $this->assertDatabaseHas('pomos', [
            'id' => $this->pomo_id,
            'notes' => 'Updated Pomo Notes',
        ]);

    }

    public function test_user_can_destroy_pomo(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->access_token,
        ])->deleteJson($this->pomo_route . $this->pomo_id);

        $response->assertOk()
            ->assertJson([
                'message' => 'Pomo deleted successfully.',
            ]);

        $this->assertDatabaseMissing('pomos', [
            'id' => $this->pomo_id,
        ]);
    }

}
