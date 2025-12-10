<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_tasks()
    {
        $user = \App\Models\User::factory()->create();
        $task = \App\Models\UserTask::create([
            'user_id' => $user->id,
            'title' => 'Test Task',
            'due_date' => now(),
            'is_completed' => false
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/user-tasks');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'Test Task']);
    }

    public function test_user_can_create_task()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/user-tasks', [
            'title' => 'New Task',
            'due_date' => now()->toDateTimeString()
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'New Task']);

        $this->assertDatabaseHas('user_tasks', ['title' => 'New Task']);
    }

    public function test_user_can_update_task()
    {
        $user = \App\Models\User::factory()->create();
        $task = \App\Models\UserTask::create([
            'user_id' => $user->id,
            'title' => 'Old Task',
            'is_completed' => false
        ]);

        $response = $this->actingAs($user)->putJson("/api/v1/user-tasks/{$task->id}", [
            'is_completed' => true
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['is_completed' => true]);
            
        $this->assertDatabaseHas('user_tasks', [
            'id' => $task->id,
            'is_completed' => true
        ]);
    }

    public function test_user_can_delete_task()
    {
        $user = \App\Models\User::factory()->create();
        $task = \App\Models\UserTask::create([
            'user_id' => $user->id,
            'title' => 'Task to Delete'
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/user-tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('user_tasks', ['id' => $task->id]);
    }
}
