<?php

namespace Tests\Feature;

use App\Models\Law;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteLawTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_and_remove_a_favorite_law(): void
    {
        $user = User::factory()->create();
        $law = Law::create([
            'source_id' => 1,
            'title' => 'Test law',
            'so_ky_hieu' => '123',
            'loai_van_ban' => 'Nghị định',
            'tinh_trang_hieu_luc' => 'Có hiệu lực',
        ]);

        $addResponse = $this->actingAs($user)->postJson('/api/favorites', ['law_id' => $law->id]);
        $addResponse->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Đã thêm vào yêu thích',
            ]);

        $this->assertDatabaseHas('favorite_laws', [
            'user_id' => $user->id,
            'law_id' => $law->id,
        ]);

        $removeResponse = $this->actingAs($user)->deleteJson('/api/favorites/' . $law->id);
        $removeResponse->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('favorite_laws', [
            'user_id' => $user->id,
            'law_id' => $law->id,
        ]);
    }

    public function test_duplicate_favorite_is_rejected_with_a_clear_message(): void
    {
        $user = User::factory()->create();
        $law = Law::create([
            'source_id' => 2,
            'title' => 'Duplicate law',
            'so_ky_hieu' => '456',
            'loai_van_ban' => 'Thông tư',
            'tinh_trang_hieu_luc' => 'Có hiệu lực',
        ]);

        $first = $this->actingAs($user)->postJson('/api/favorites', ['law_id' => $law->id]);
        $first->assertOk();

        $second = $this->actingAs($user)->postJson('/api/favorites', ['law_id' => $law->id]);
        $second->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Văn bản đã có trong danh sách yêu thích',
            ]);
    }
}
