<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Master;
use App\Models\RepairBooking;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    public function test_public_pages_render_and_keep_private_navigation_hidden(): void
    {
        $master = Master::factory()->create();
        $article = Article::factory()->published()->create();
        foreach (['/', '/about', '/contacts', '/privacy', '/masters', '/masters/'.$master->slug, '/articles', '/articles/'.$article->id, '/galery/0', '/login', '/signin'] as $path) {
            $this->get($path)->assertOk()->assertSee('Почин')->assertDontSee('href="'.route('workspace').'"', false);
        }
    }

    public function test_article_and_approved_comment_html_is_escaped(): void
    {
        $article = Article::factory()->published()->create(['body' => '<script>alert("article")</script>']);
        Comment::factory()->approved()->create(['article_id' => $article->id, 'body' => '<img src=x onerror=alert(1)>']);
        $this->get('/articles/'.$article->id)->assertOk()->assertSee('&lt;script&gt;', false)->assertSee('&lt;img', false)->assertDontSee('<script>alert("article")</script>', false)->assertDontSee('<img src=x', false);
    }

    public function test_reader_can_render_request_account_and_own_history(): void
    {
        $user = User::factory()->create();
        Master::factory()->create();
        $repair = RepairBooking::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get('/request')->assertOk()->assertSee('name="submission_key"', false);
        $this->get('/account')->assertOk()->assertSee($repair->device);
        $this->get('/repairs/'.$repair->id)->assertOk()->assertSee($repair->device);
    }

    public function test_moderator_pages_render_with_real_data(): void
    {
        $role = Role::factory()->create(['name' => 'moderator']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $master = Master::factory()->create();
        $article = Article::factory()->published()->create();
        Comment::factory()->create(['article_id' => $article->id]);
        RepairBooking::factory()->create();
        foreach (['/workspace', '/workspace/repairs', '/workspace/masters', '/workspace/masters/create', '/workspace/masters/'.$master->slug.'/edit', '/moderation', '/articles/create', '/articles/'.$article->id.'/edit'] as $path) {
            $this->actingAs($user)->get($path)->assertOk()->assertSee('Рабочее место');
        }
    }

    public function test_inactive_master_and_draft_are_not_public(): void
    {
        $master = Master::factory()->create(['is_active' => false]);
        $article = Article::factory()->create(['status' => 'draft']);
        $this->get('/masters/'.$master->slug)->assertNotFound();
        $this->get('/articles/'.$article->id)->assertNotFound();
    }

    public function test_real_csrf_protection_rejects_registration_without_token(): void
    {
        $this->app['env'] = 'local';
        $this->post('/signin', ['name' => 'Гость', 'email' => 'csrf@example.test', 'password' => 'SomePassword123', 'password_confirmation' => 'SomePassword123'])->assertStatus(419);
        $this->assertDatabaseMissing('users', ['email' => 'csrf@example.test']);
    }
}
