<?php

namespace Tests\Feature;

use App\Models\Master;
use App\Models\RepairBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class RepairWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge(['submission_key' => (string) Str::uuid(), 'service' => 'diagnostics', 'device' => 'Ноутбук 14', 'description' => 'Не включается. Индикатор зарядки работает, экран остаётся тёмным.', 'contact_name' => 'Клиент', 'phone' => '+7 (999) 000-00-00', 'consent' => '1'], $overrides);
    }

    public function test_repair_creation_records_owner_pending_state_private_photos_and_event(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $other = User::factory()->create();
        $master = Master::factory()->create();
        $this->actingAs($user)->post('/request', $this->payload(['master_id' => $master->id, 'user_id' => $other->id, 'status' => 'ready', 'quoted_price' => 1, 'photos' => [UploadedFile::fake()->image('device.jpg')]]))->assertRedirect();
        $repair = RepairBooking::firstOrFail();
        $this->assertSame($user->id, $repair->user_id);
        $this->assertSame('new', $repair->status);
        $this->assertNull($repair->quoted_price);
        $this->assertSame('+79990000000', $repair->phone);
        $this->assertCount(1, $repair->events);
        Storage::disk('local')->assertExists($repair->photos[0]);
        $this->get('/repairs/'.$repair->id.'/photos/0')->assertOk()->assertHeader('Cache-Control', 'no-store, private');
        $this->actingAs($other)->get('/repairs/'.$repair->id.'/photos/0')->assertForbidden();
        $this->get('/repairs/'.$repair->id)->assertForbidden();
    }

    public function test_repeated_submission_is_idempotent_and_does_not_duplicate_photos_or_events(): void
    {
        $payload = $this->payload();
        $this->actingAs(User::factory()->create())->post('/request', $payload)->assertRedirect();
        $this->post('/request', $payload)->assertRedirect();
        $this->assertDatabaseCount('repair_bookings', 1);
        $this->assertDatabaseCount('repair_events', 1);
    }

    public function test_invalid_service_inactive_or_incompatible_master_and_missing_consent_are_rejected(): void
    {
        $user = User::factory()->create();
        $master = Master::factory()->create(['services' => ['screen']]);
        $this->actingAs($user)->post('/request', $this->payload(['master_id' => $master->id]))->assertSessionHasErrors('master_id');
        $this->post('/request', $this->payload(['service' => 'fake', 'consent' => '0']))->assertSessionHasErrors(['service', 'consent']);
        $master->update(['is_active' => false]);
        $this->post('/request', $this->payload(['master_id' => $master->id]))->assertSessionHasErrors('master_id');
        $this->assertDatabaseCount('repair_bookings', 0);
    }

    public function test_quote_approval_belongs_to_owner_and_rejects_stale_price(): void
    {
        $repair = RepairBooking::factory()->create(['status' => 'approval', 'quoted_price' => 4500]);
        $this->actingAs(User::factory()->create())->patch('/repairs/'.$repair->id.'/approve', ['consent' => '1', 'quoted_price' => 4500])->assertForbidden();
        $this->actingAs($repair->user)->patch('/repairs/'.$repair->id.'/approve', ['consent' => '1', 'quoted_price' => 4000])->assertSessionHasErrors('quoted_price');
        $this->assertSame('approval', $repair->fresh()->status);
        $this->patch('/repairs/'.$repair->id.'/approve', ['consent' => '1', 'quoted_price' => 4500])->assertRedirect();
        $this->assertSame('repairing', $repair->fresh()->status);
        $this->assertNotNull($repair->fresh()->quote_accepted_at);
    }

    public function test_workspace_enforces_transitions_and_customer_price_consent(): void
    {
        $repair = RepairBooking::factory()->create(['status' => 'diagnosing']);
        $this->actingAs($this->moderator())->patch('/workspace/repairs/'.$repair->id, ['status' => 'ready', 'master_id' => $repair->master_id])->assertSessionHasErrors('status');
        $this->patch('/workspace/repairs/'.$repair->id, ['status' => 'approval', 'master_id' => $repair->master_id])->assertSessionHasErrors('quoted_price');
        $this->patch('/workspace/repairs/'.$repair->id, ['status' => 'approval', 'master_id' => $repair->master_id, 'quoted_price' => 4500, 'public_message' => 'Замена детали', 'internal_note' => 'Внутренний код поставщика'])->assertRedirect();
        $this->assertSame('approval', $repair->fresh()->status);
        $this->patch('/workspace/repairs/'.$repair->id, ['status' => 'repairing', 'master_id' => $repair->master_id, 'quoted_price' => 4500])->assertSessionHasErrors('status');
        $this->assertStringNotContainsString('Внутренний код поставщика', $repair->events()->get()->toJson());
        $this->assertArrayNotHasKey('internal_note', $repair->fresh()->toArray());
    }

    public function test_accepted_quote_cannot_be_changed_and_own_cancellation_is_limited(): void
    {
        $repair = RepairBooking::factory()->create(['status' => 'repairing', 'quoted_price' => 4500, 'quote_accepted_at' => now()]);
        $this->actingAs($this->moderator())->patch('/workspace/repairs/'.$repair->id, ['status' => 'ready', 'master_id' => $repair->master_id, 'quoted_price' => '4500'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('ready', $repair->fresh()->status);
        $this->patch('/workspace/repairs/'.$repair->id, ['status' => 'closed', 'master_id' => $repair->master_id, 'quoted_price' => 5000])->assertSessionHasErrors('quoted_price');
        $this->actingAs($repair->user)->patch('/repairs/'.$repair->id.'/cancel')->assertSessionHasErrors('status');
        $early = RepairBooking::factory()->create(['user_id' => $repair->user_id]);
        $this->patch('/repairs/'.$early->id.'/cancel')->assertRedirect();
        $this->assertSame('cancelled', $early->fresh()->status);
    }

    public function test_account_filters_always_keep_owner_scope(): void
    {
        $user = User::factory()->create();
        RepairBooking::factory()->create(['user_id' => $user->id, 'status' => 'new']);
        RepairBooking::factory()->create(['status' => 'new']);
        $this->actingAs($user)->get('/account?status=new')->assertViewHas('repairs', fn ($repairs): bool => $repairs->total() === 1 && $repairs->first()->user_id === $user->id);
    }

    public function test_master_filter_uses_service_and_active_status(): void
    {
        Master::factory()->create(['services' => ['screen'], 'is_active' => true]);
        Master::factory()->create(['services' => ['screen'], 'is_active' => false]);
        Master::factory()->create(['services' => ['cooling']]);
        $this->get('/masters?service=screen')->assertViewHas('masters', fn ($masters): bool => $masters->total() === 1);
    }
}
