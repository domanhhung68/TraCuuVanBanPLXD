<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_display_name_and_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'New Name',
            'current_password' => 'password123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/profile')
            ->assertSessionHas('success');

        $user->refresh();

        $this->assertSame('New Name', $user->name);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }
}
