<?php

namespace Tests\Feature\Project;

use App\Models\Pomo;
use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PomoCRUDTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private Todo $todo;
    private Pomo $pomo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($user = User::factory()->create());
        $this->user = $user;
        $this->assertAuthenticated();
        $this->todo = Todo::factory()->create();

    }

    public function test_user_can_create_pomo(): void
    {
        $now = now();
        $end = now()->addMinutes(25);
        // Create a pomo through POST and store it in the pomo property
        $response = $this->post(route('pomo.store'), [
            'todo_id' => $this->todo->id,
            'notes' => 'Test Notes',
            'pomo_start' => $now,
            'pomo_end' => $end,
        ]);
        $response->assertRedirect(route('pomo.index'));
        $this->assertDatabaseHas('pomos', [
            'todo_id' => $this->todo->id,
            'notes' => 'Test Notes',
            'pomo_start' => strtotime($now),
            'pomo_end' => strtotime($end),
        ]);

        $this->pomo = Pomo::where('todo_id', $this->todo->id)->first();
    }

    public function test_user_can_view_pomo(): void
    {
        $this->test_user_can_create_pomo();
        $this->assertDatabaseHas('pomos', [
            'todo_id' => $this->todo->id,
            'notes' => 'Test Notes',
        ]);

    }

    public function test_user_can_update_pomo_with_authorsation(): void
    {
        $this->test_user_can_create_pomo();
        $now = now();
        $end = now()->addMinutes(25);
        $response = $this->put(route('pomo.update', $this->pomo->id), [
            'todo_id' => $this->todo->id,
            'notes' => 'Test Notes Updated',
            'pomo_start' => $now,
            'pomo_end' => $end,
        ]);
        $response->assertRedirect(route('pomo.index'));
        $this->assertDatabaseHas('pomos', [
            'todo_id' => $this->todo->id,
            'notes' => 'Test Notes Updated',
            'pomo_start' => strtotime($now),
            'pomo_end' => strtotime($end),
        ]);
    }

    public function test_user_can_delete_pomo_with_authorsation(): void
    {
        $this->test_user_can_create_pomo();
        $response = $this->delete(route('pomo.destroy', $this->pomo->id));
        $response->assertRedirect(route('pomo.index'));
        $this->assertDatabaseMissing('pomos', [
            'todo_id' => $this->todo->id,
            'notes' => 'Test Notes',
        ]);
    }

}
