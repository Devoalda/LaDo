<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Gets redirected to login page if not logged in
        // Gets redirected to project page if logged in
        if (auth()->check()) {
            $response = $this->get(route('project.index'));
        } else {
            $response = $this->get(route('login'));
        }
        $response->assertStatus(200);
    }
}
