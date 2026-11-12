<?php

namespace Tests\Feature;

use App\Jobs\VeryLongJob;
use App\Mail\ArticleCreated;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Policies\ArticlePolicy;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LabsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    public function test_lab_one_public_layout_and_dynamic_contacts(): void
    {
        $this->get('/')->assertOk()->assertViewIs('home');
        $this->get('/about')->assertOk()->assertViewIs('pages.about');
        $this->get('/contacts')->assertOk()->assertViewHas('contacts', fn (array $contacts): bool => $contacts['Почта'] === config('site.email') && count($contacts) === 4);
    }

    public function test_lab_two_json_entries_and_full_image_gallery(): void
    {
        $entries = json_decode(file_get_contents(public_path('articles.json')), true, 512, JSON_THROW_ON_ERROR);
        $this->get('/')->assertViewHas('entries', $entries);
        foreach ($entries as $index => $entry) {
            $this->assertFileExists(public_path($entry['preview_image']));
            $this->assertFileExists(public_path($entry['full_image']));
            $this->get('/galery/'.$index)->assertOk()->assertViewHas('entry', $entry);
        }
        $this->get('/galery/999')->assertNotFound();
        $this->get('/galery/not-a-number')->assertNotFound();
    }

    public function test_lab_three_registration_json_excludes_password_and_ignores_role_injection(): void
    {
        $moderator = $this->moderator();
        $this->postJson('/signin', ['name' => 'Новый пользователь', 'email' => 'new@pochin.test', 'password' => 'StrongPass123!', 'password_confirmation' => 'StrongPass123!', 'role_id' => $moderator->role_id, 'is_blocked' => true])
            ->assertCreated()->assertJsonPath('user.email', 'new@pochin.test')->assertJsonMissingPath('user.password')->assertJsonMissingPath('user.role_id');
        $user = User::where('email', 'new@pochin.test')->firstOrFail();
        $this->assertSame('reader', $user->role->name);
        $this->assertFalse($user->is_blocked);
        $this->assertNotSame('StrongPass123!', $user->password);
    }

    public function test_registration_requires_valid_unique_fields_and_confirmation(): void
    {
        $user = User::factory()->create();
        $this->postJson('/signin', ['name' => '', 'email' => $user->email, 'password' => 'short', 'password_confirmation' => 'other'])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_regular_registration_redirects_to_login(): void
    {
        $this->post('/signin', ['name' => 'Клиент', 'email' => 'browser@pochin.test', 'password' => 'StrongPass123!', 'password_confirmation' => 'StrongPass123!'])->assertRedirect(route('login'));
    }

    public function test_lab_four_factory_and_published_article_list(): void
    {
        Article::factory()->published()->count(12)->create();
        Article::factory()->create(['title' => 'Скрытый материал']);
        $this->get('/articles')->assertOk()->assertViewHas('articles', fn ($articles): bool => $articles->total() === 12 && $articles->count() === 9);
    }

    public function test_lab_five_moderator_article_crud_and_cover_upload(): void
    {
        Storage::fake('public');
        $user = $this->moderator();
        $this->actingAs($user)->get('/articles/create')->assertOk();
        $this->post('/articles', ['title' => 'Новый материал', 'excerpt' => 'Краткое описание', 'body' => 'Полный текст материала о диагностике.', 'category' => 'Ремонт', 'status' => 'published', 'cover' => UploadedFile::fake()->image('cover.jpg')])->assertRedirect();
        $article = Article::firstOrFail();
        $this->assertSame($user->id, $article->user_id);
        Storage::disk('public')->assertExists($article->cover);
        $this->get('/articles/'.$article->id.'/edit')->assertOk();
        $this->put('/articles/'.$article->id, ['title' => 'Изменённый материал', 'excerpt' => 'Описание', 'body' => 'Новый текст', 'category' => 'Уход', 'status' => 'draft'])->assertRedirect();
        $this->assertSame('Изменённый материал', $article->fresh()->title);
        $this->delete('/articles/'.$article->id)->assertRedirect(route('articles.index'));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
        Storage::disk('public')->assertMissing($article->cover);
    }

    public function test_lab_five_validation_prevents_empty_article(): void
    {
        $this->actingAs($this->moderator())->post('/articles', ['title' => ''])->assertSessionHasErrors(['title', 'excerpt', 'body', 'category', 'status']);
        $this->assertDatabaseCount('articles', 0);
    }

    public function test_lab_six_login_creates_sanctum_token_and_logout_revokes_it(): void
    {
        $user = User::factory()->create(['password' => 'StrongPass123!']);
        $this->post('/login', ['email' => $user->email, 'password' => 'StrongPass123!'])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, $user->tokens()->count());
        $this->assertNotNull(session('auth_token_id'));
        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_lab_seven_gate_before_overrides_policy_for_moderator_only(): void
    {
        $moderator = $this->moderator();
        $this->assertFalse((new ArticlePolicy)->create($moderator)->allowed());
        $this->actingAs($moderator)->get('/articles/create')->assertOk();
        $this->actingAs(User::factory()->create())->get('/articles/create')->assertForbidden();
        $this->get('/moderation')->assertForbidden();
    }

    public function test_lab_seven_role_seeder_creates_roles_and_moderator_relation(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::where('email', 'moderator@pochin.test')->firstOrFail();
        $this->assertSame('moderator', $user->role->name);
        $this->assertTrue($user->isModerator());
        $this->assertDatabaseHas('roles', ['name' => 'reader']);
    }

    public function test_lab_eight_sends_mailable_to_active_moderator_and_uses_build(): void
    {
        Queue::fake();
        $moderator = $this->moderator();
        $this->actingAs($moderator)->post('/articles', ['title' => 'Письмо модератору', 'excerpt' => 'Кратко', 'body' => 'Текст статьи.', 'category' => 'Ремонт', 'status' => 'draft'])->assertRedirect();
        $article = Article::firstOrFail();
        Queue::assertPushed(VeryLongJob::class, fn (VeryLongJob $job): bool => $job->articleId === $article->id && $job->afterCommit);
        (new VeryLongJob($article->id))->handle();
        Mail::assertSent(ArticleCreated::class, fn (ArticleCreated $mail): bool => $mail->hasTo($moderator->email) && $mail->article->id === $article->id);
        $mail = (new ArticleCreated($article))->build();
        $this->assertSame('mail.article-created', $mail->view);
        $this->assertStringContainsString($article->title, $mail->subject);
    }

    public function test_mail_failure_does_not_undo_article_creation(): void
    {
        Queue::fake();
        $moderator = $this->moderator();
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Transport unavailable'));
        $this->actingAs($moderator)
            ->post('/articles', ['title' => 'Без почты', 'excerpt' => 'Кратко', 'body' => 'Текст статьи.', 'category' => 'Ремонт', 'status' => 'draft'])
            ->assertRedirect();
        $article = Article::where('title', 'Без почты')->firstOrFail();
        (new VeryLongJob($article->id))->handle();
        $this->assertModelExists($article);
    }

    public function test_lab_nine_pending_comment_message_and_moderation_actions(): void
    {
        $article = Article::factory()->published()->create();
        $reader = User::factory()->create();
        $this->actingAs($reader)->post('/articles/'.$article->id.'/comments', ['body' => 'Спасибо за понятное объяснение.', 'status' => 'approved', 'is_approved' => true])->assertRedirect()->assertSessionHas('success', 'Комментарий ожидает модерации.');
        $comment = Comment::firstOrFail();
        $this->assertFalse($comment->is_approved);
        $this->patch('/comments/'.$comment->id.'/approve')->assertForbidden();
        $this->actingAs($this->moderator())->get('/moderation')->assertViewHas('comments', fn ($comments): bool => $comments->total() === 1);
        $this->patch('/comments/'.$comment->id.'/approve')->assertRedirect();
        $this->assertTrue($comment->fresh()->is_approved);
        $this->get('/articles/'.$article->id)->assertSee('Спасибо за понятное объяснение.');
        $rejected = Comment::factory()->create(['article_id' => $article->id]);
        $this->patch('/comments/'.$rejected->id.'/reject')->assertRedirect();
        $this->assertModelMissing($rejected);
    }

    public function test_article_search_pagination_keeps_filters_and_hides_drafts(): void
    {
        Article::factory()->published()->count(11)->create(['title' => 'Уход за ноутбуком', 'category' => 'Уход']);
        $draft = Article::factory()->create(['title' => 'Уход скрытый', 'category' => 'Уход']);
        $this->get('/articles?q=Уход&category=Уход&page=2')->assertViewHas('articles', fn ($articles): bool => $articles->total() === 11 && $articles->count() === 2 && str_contains($articles->url(1), 'q='));
        $this->get('/articles/'.$draft->id)->assertNotFound();
    }
}
