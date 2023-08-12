<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected array $user = [
        'name' => 'Test User',
        'email' => 'test@mail.com',
        'password' => 'password1234',
    ];
    protected array $auth_struct = [
        'access_token',
        'token_type',
    ];

    protected string $registerRoute = '/api/register';
    protected string $loginRoute = '/api/login';
    protected string $meRoute = '/api/me';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    protected function registerAndLoginUser(): string
    {
        $this->postJson($this->registerRoute, $this->user);

        $response = $this->postJson($this->loginRoute, [
            'email' => $this->user['email'],
            'password' => $this->user['password'],
        ]);

        return $response['access_token'];
    }

    public function test_user_can_register_with_api(): void
    {
        $response = $this->postJson($this->registerRoute, $this->user);

        $response->assertCreated()->assertJsonStructure($this->auth_struct);
    }

    public function test_user_can_authenticate_with_api(): void
    {
        $this->test_user_can_register_with_api();

        $response = $this->postJson($this->loginRoute, [
            'email' => $this->user['email'],
            'password' => $this->user['password'],
        ]);

        $response->assertOk()->assertJsonStructure($this->auth_struct);
    }

    public function test_user_can_view_me(): void
    {
        $token = $this->registerAndLoginUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson($this->meRoute);

        $response->assertOk()->assertJsonStructure([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
        ]);
    }
}
