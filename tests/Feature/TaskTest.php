<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use app\Http\Controllers\TaskController;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
	     public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
     */

use RefreshDatabase; // ✅ Ensures a clean database for each test

    public function test_create_task()
    {
        $user = User::factory()->create(); // ✅ Create a user

        $response = $this->actingAs($user)->postJson('/api/tasks', [ // ✅ Authenticate user
            'title' => 'Test Task',
            'description' => 'This is a test task'
        ]);

        $response->assertStatus(201); // ✅ Expecting success

        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']); // ✅ Check DB entry
    }

}
