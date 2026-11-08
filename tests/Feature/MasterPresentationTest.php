<?php

namespace Tests\Feature;

use App\Models\Master;
use Database\Seeders\MasterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterPresentationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_all_demo_masters_have_distinct_local_portraits_and_matching_examples(): void
    {
        $this->seed(MasterSeeder::class);
        $urls = [];
        foreach (Master::all() as $master) {
            $this->assertNotNull($master->avatar_url);
            $this->assertFileExists(public_path('images/masters/'.$master->slug.'.webp'));
            $this->assertFileExists(public_path('images/masters/'.$master->slug.'-thumb.webp'));
            $urls[] = $master->avatar_url;
            $this->assertNotEmpty($master->work_examples);
            foreach ($master->work_examples as $example) {
                $this->assertNotEmpty(array_intersect($master->services, $example['services']));
                $this->assertFileExists(public_path($example['image']));
            }
            $this->get('/masters/'.$master->slug)->assertOk()->assertSee($master->name)->assertSee('work-example-grid', false)->assertSee($master->avatar_thumbnail_url, false);
        }
        $this->assertCount(6, array_unique($urls));
    }

    public function test_new_master_without_photo_keeps_initials_and_does_not_request_missing_image(): void
    {
        $master = Master::factory()->create(['name' => 'Новый Мастер', 'slug' => 'new-master']);
        $this->assertNull($master->avatar_url);
        $this->get('/masters/'.$master->slug)->assertOk()->assertSee('НМ')->assertDontSee('images/masters/new-master.webp', false);
    }

    public function test_examples_are_selected_by_specialization(): void
    {
        $master = Master::factory()->create(['services' => ['screen']]);
        $this->assertCount(1, $master->work_examples);
        $this->assertSame('images/work/screen-service.webp', $master->work_examples[0]['image']);
        $this->get('/masters/'.$master->slug)->assertOk()->assertSee('Полосы или мерцание экрана')->assertDontSee('Ноутбук греется и шумит');
    }
}
