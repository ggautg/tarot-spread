<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_save_a_theme(): void
    {
        $this->patch('/tema', ['theme' => 'merlin'])->assertRedirect('/login');
    }

    public function test_authenticated_users_can_save_a_theme(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/')
            ->patch('/tema', ['theme' => 'crowley']);

        $response->assertSessionHasNoErrors()->assertRedirect('/');

        $this->assertSame('crowley', $user->refresh()->theme);
    }

    public function test_theme_slug_is_validated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/')
            ->patch('/tema', ['theme' => 'Merlin<script>']);

        $response->assertSessionHasErrors('theme');

        $this->assertSame('fortune', $user->refresh()->theme);
    }

    public function test_new_users_get_the_default_theme(): void
    {
        $user = User::factory()->create();

        $this->assertSame('fortune', $user->refresh()->theme);
    }
}
