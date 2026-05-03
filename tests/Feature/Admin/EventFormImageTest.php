<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventFormImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_event_with_uploaded_image_persists_path(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post('/extranet/events', [
            'title' => 'Avec image',
            'start_date' => now()->addWeek()->format('Y-m-d H:i'),
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->image('cover.jpg', 800, 600),
        ]);

        $response->assertRedirect();
        $event = Event::query()->latest('id')->first();
        $this->assertNotNull($event->featured_image);
        $this->assertStringStartsWith('events/images/', $event->featured_image);
        Storage::disk('public')->assertExists($event->featured_image);
    }

    public function test_update_event_replaces_old_image(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        // Crée d'abord une image qui simule l'ancienne
        $oldPath = 'events/images/old.jpg';
        Storage::disk('public')->put($oldPath, 'old content');
        $event = Event::create([
            'organizer_id' => $admin->id,
            'title' => 'Existant',
            'slug' => 'existant',
            'start_date' => now()->addWeek(),
            'featured_image' => $oldPath,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->put("/extranet/events/{$event->id}", [
            'title' => 'Existant',
            'start_date' => now()->addWeek()->format('Y-m-d H:i'),
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->image('new.jpg', 800, 600),
        ]);

        $response->assertRedirect();
        $event->refresh();
        $this->assertNotSame($oldPath, $event->featured_image);
        $this->assertStringStartsWith('events/images/', $event->featured_image);
        Storage::disk('public')->assertExists($event->featured_image);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_validation_rejects_non_image_file(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post('/extranet/events', [
            'title' => 'Mauvais format',
            'start_date' => now()->addWeek()->format('Y-m-d H:i'),
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('featured_image');
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }
}
