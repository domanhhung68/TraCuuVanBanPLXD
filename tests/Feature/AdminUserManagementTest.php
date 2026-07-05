<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Người dùng mới',
            'email' => 'newuser@example.com',
            'password' => '12345678',
            'role' => 'customer',
        ]);

        $response->assertRedirect(route('admin.ui.users'));
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_admin_can_update_existing_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Tên đã sửa',
            'email' => 'updated@example.com',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.ui.users'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Tên đã sửa',
            'email' => 'updated@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_change_user_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'password' => 'new-password-123',
        ]);

        $response->assertRedirect(route('admin.ui.users'));
        $user->refresh();
        $this->assertTrue(Hash::check('new-password-123', $user->password));
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.ui.users'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
