<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    public function test_guest_cannot_enter_account_or_mutate_articles(): void
    {
        foreach (['/account', '/request', '/workspace', '/moderation', '/articles/create'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
        $this->post('/articles', [])->assertRedirect(route('login'));
    }

    public function test_reader_cannot_access_workspace_or_master_management(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['/workspace', '/workspace/masters', '/workspace/masters/create', '/workspace/repairs'] as $path) {
            $this->get($path)->assertForbidden();
        }
    }

    public function test_blocked_user_cannot_login_or_keep_existing_session_and_tokens(): void
    {
        $user = User::factory()->create(['is_blocked' => true, 'password' => 'StrongPass123!']);
        $this->post('/login', ['email' => $user->email, 'password' => 'StrongPass123!'])->assertSessionHasErrors('email');
        $user->createToken('old');
        $this->actingAs($user)->get('/account')->assertForbidden();
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_forged_owner_fields_cannot_change_existing_article_author(): void
    {
        $article = Article::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($this->moderator())->put('/articles/'.$article->id, ['title' => 'Безопасный заголовок', 'excerpt' => 'Описание', 'body' => 'Текст', 'category' => 'Ремонт', 'status' => 'draft', 'user_id' => $other->id])->assertRedirect();
        $this->assertSame($article->user_id, $article->fresh()->user_id);
    }

    public function test_moderator_command_promotes_only_existing_account(): void
    {
        $user = User::factory()->create();
        $this->artisan('pochin:moderator', ['email' => $user->email])->assertSuccessful();
        $this->assertTrue($user->fresh()->isModerator());
        $this->artisan('pochin:moderator', ['email' => 'missing@pochin.test'])->assertFailed();
        $this->assertDatabaseMissing('users', ['email' => 'missing@pochin.test']);
    }

    public function test_bulk_comment_moderation_uses_only_selected_pending_records(): void
    {
        $comments = Comment::factory()->count(3)->create();
        $this->actingAs($this->moderator())->post('/moderation/bulk', ['ids' => [$comments[0]->id, $comments[1]->id], 'action' => 'approve'])->assertRedirect();
        $this->assertSame(2, Comment::where('is_approved', true)->count());
        $this->assertFalse($comments[2]->fresh()->is_approved);
    }
}
