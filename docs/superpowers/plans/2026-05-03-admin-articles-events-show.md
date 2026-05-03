# Admin Articles & Events show + form events — Plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refondre les pages admin show pour articles et events (afficher image cover + rendre HTML correctement + sidebar méta enrichie en layout `1fr 2fr`), aligner le form events sur le pattern d'articles (input image + Quill).

**Architecture:** 4 vues blade refaites/modifiées (`show.blade.php` × 2 + `_form.blade.php` + `edit/create.blade.php`), 1 controller modifié (`EventController` pour upload image), 0 migration. Toutes les colonnes nécessaires existent déjà. Réutilise le pattern Quill v2 et image upload d'`ArticleController`/`articles/_form.blade.php` à l'identique.

**Tech Stack:** Laravel 12, Blade, Quill v2 via CDN, Storage local avec symlink `public/storage`, classes admin existantes (`.card`, `.badge`, `.form-input`).

**Spec source:** `docs/superpowers/specs/2026-05-03-admin-articles-events-show-design.md`

---

## File map

**Modified:**
- `resources/views/admin/articles/show.blade.php` — refonte complète layout `1fr 2fr` avec image + rendu HTML correct
- `resources/views/admin/events/show.blade.php` — refonte complète layout `1fr 2fr` avec image + dates + lieu + rendu HTML
- `resources/views/admin/events/_form.blade.php` — ajout input image + Quill (remplace textarea content)
- `resources/views/admin/events/edit.blade.php` — ajout `enctype="multipart/form-data"` sur `<form>` si absent
- `resources/views/admin/events/create.blade.php` — idem
- `app/Http/Controllers/Admin/EventController.php` — `store()` et `update()` : ajout validation `featured_image` + logique upload + delete ancien fichier

**Created:**
- `tests/Feature/Admin/ArticleShowAdminTest.php` — 4 tests
- `tests/Feature/Admin/EventShowAdminTest.php` — 4 tests
- `tests/Feature/Admin/EventFormImageTest.php` — 3 tests

---

## Task 1 — Refonte show admin Article

**Files:**
- Modify: `resources/views/admin/articles/show.blade.php` (réécriture complète)
- Create: `tests/Feature/Admin/ArticleShowAdminTest.php` (4 tests)

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/Admin/ArticleShowAdminTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleShowAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_renders_featured_image_when_present(): void
    {
        $admin = $this->makeAdmin();
        $article = Article::create([
            'author_id' => $admin->id,
            'title' => 'Test article',
            'slug' => 'test-article',
            'content' => '<p>Contenu</p>',
            'featured_image' => 'articles/images/example.jpg',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/articles/{$article->id}");

        $response->assertOk()
            ->assertSee('articles/images/example.jpg', escape: false);
    }

    public function test_show_does_not_render_image_block_when_absent(): void
    {
        $admin = $this->makeAdmin();
        $article = Article::create([
            'author_id' => $admin->id,
            'title' => 'Sans image',
            'slug' => 'sans-image',
            'content' => '<p>Pas d\'image</p>',
            'featured_image' => null,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/articles/{$article->id}");

        $response->assertOk()
            ->assertDontSee('articles/images/', escape: false);
    }

    public function test_show_renders_content_html_correctly(): void
    {
        $admin = $this->makeAdmin();
        $article = Article::create([
            'author_id' => $admin->id,
            'title' => 'HTML rendu',
            'slug' => 'html-rendu',
            'content' => '<p>Bonjour <strong>monde</strong></p>',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/articles/{$article->id}");

        $response->assertOk()
            ->assertSee('<strong>monde</strong>', escape: false)
            ->assertDontSee('&lt;strong&gt;', escape: false);
    }

    public function test_show_displays_validation_block_when_validated(): void
    {
        $admin = $this->makeAdmin();
        $validator = User::factory()->create(['name' => 'Jean Dupont']);
        $article = Article::create([
            'author_id' => $admin->id,
            'title' => 'Validé',
            'slug' => 'valide',
            'content' => '<p>Validé</p>',
            'status' => 'validated',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'validation_notes' => 'OK pour publication',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/articles/{$article->id}");

        $response->assertOk()
            ->assertSee('Jean Dupont')
            ->assertSee('OK pour publication');
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }
}
```

- [ ] **Step 2: Run tests, expect failure**

`php artisan test --filter=ArticleShowAdminTest`
Expected: tests 1, 2, 3 fail (current page doesn't render image, has `nl2br(e())` bug). Test 4 may pass (validator was already shown).

- [ ] **Step 3: Replace `show.blade.php` content**

Replace the entire content of `resources/views/admin/articles/show.blade.php` with:

```blade
@extends('layouts.admin')
@section('title', $article->title)
@section('breadcrumb')
    <a href="{{ route('admin.articles.index') }}">Articles</a>
    <span>/</span>
    <span>{{ Str::limit($article->title, 30) }}</span>
@endsection

@push('styles')
<style>
    .article-content { line-height: 1.7; color: #1C2B27; font-size: 1rem; }
    .article-content p { margin: 0 0 1em; }
    .article-content h2 { font-size: 1.5rem; font-weight: 700; margin: 1.5em 0 0.5em; color: #16302B; }
    .article-content h3 { font-size: 1.25rem; font-weight: 700; margin: 1.2em 0 0.4em; color: #16302B; }
    .article-content ul, .article-content ol { margin: 0 0 1em 1.5em; padding: 0; }
    .article-content li { margin-bottom: 0.25em; }
    .article-content blockquote { border-left: 3px solid #85B79D; padding: 0.25em 0 0.25em 1em; color: #68756F; margin: 1em 0; font-style: italic; }
    .article-content a { color: #356B8A; text-decoration: underline; }
    .article-content strong { font-weight: 700; }
    .article-content em { font-style: italic; }
    .article-content hr { border: none; border-top: 1px solid #e5e7eb; margin: 2em 0; }
</style>
@endpush

@section('content')
    {{-- Header actions --}}
    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-secondary">Modifier</a>
        @if($article->status === 'published')
            <a href="{{ route('hub.articles.show', $article) }}" target="_blank" class="btn btn-primary">Voir côté public ↗</a>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        {{-- SIDEBAR MÉTA --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">Informations</h3></div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    @switch($article->status)
                        @case('published') <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1rem;">Publié</span> @break
                        @case('validated') <span class="badge badge-info" style="font-size: 1rem; padding: 0.5rem 1rem;">Validé</span> @break
                        @case('submitted') <span class="badge badge-warning" style="font-size: 1rem; padding: 0.5rem 1rem;">Soumis</span> @break
                        @default <span class="badge badge-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">Brouillon</span>
                    @endswitch
                    @if($article->is_featured)
                        <span class="badge badge-info" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-left: 0.5rem;">Vedette</span>
                    @endif
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Vues</div>
                    <div style="font-weight: 500;">{{ number_format($article->views_count ?? 0, 0, ',', ' ') }}</div>
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Auteur</div>
                    <div>{{ $article->author?->name ?? 'Non défini' }}</div>
                </div>

                @if($article->category)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Catégorie</div>
                        <div>{{ $article->category }}</div>
                    </div>
                @endif

                @if($article->slug)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Slug</div>
                        <code style="background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.8125rem;">{{ $article->slug }}</code>
                    </div>
                @endif

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Créé le</div>
                    <div>{{ $article->created_at->format('d/m/Y H:i') }}</div>
                </div>

                @if($article->published_at)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Publié le</div>
                        <div>{{ $article->published_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif

                @if($article->validated_at)
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Validation</div>
                        <div style="font-size: 0.875rem;">
                            Validé le {{ $article->validated_at->format('d/m/Y H:i') }}
                            @if($article->validator)
                                par <strong>{{ $article->validator->name }}</strong>
                            @endif
                        </div>
                        @if($article->validation_notes)
                            <blockquote style="margin: 0.75rem 0 0; padding: 0.5rem 0.75rem; border-left: 3px solid #85B79D; background: #f9fafb; font-style: italic; color: #4b5563; font-size: 0.875rem;">
                                {{ $article->validation_notes }}
                            </blockquote>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- BODY --}}
        <div class="card">
            <div class="card-body">
                @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" alt="" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                @endif

                <h1 style="font-size: 1.875rem; font-weight: 700; color: #16302B; margin: 0 0 1rem;">{{ $article->title }}</h1>

                @if($article->summary)
                    <p style="font-size: 1.125rem; font-style: italic; color: #6b7280; margin-bottom: 1.5rem;">{{ $article->summary }}</p>
                @endif

                <div class="article-content">
                    {!! $article->content !!}
                </div>

                @if($article->document_path)
                    <div style="margin-top: 2rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                        <i data-lucide="file-text" style="width: 24px; height: 24px; color: #356B8A;"></i>
                        <span style="flex: 1; font-weight: 500;">{{ $article->document_name ?? basename($article->document_path) }}</span>
                        <a href="{{ Storage::url($article->document_path) }}" target="_blank" class="btn btn-secondary" style="padding: 0.375rem 0.75rem;">Télécharger</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
```

- [ ] **Step 4: Run tests, expect pass**

`php artisan test --filter=ArticleShowAdminTest`
Expected: 4 passing.

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/articles/show.blade.php tests/Feature/Admin/ArticleShowAdminTest.php
git commit -m "feat(admin): refonte page show article (image cover, HTML rendu, sidebar méta)"
```

---

## Task 2 — Refonte show admin Event

**Files:**
- Modify: `resources/views/admin/events/show.blade.php` (réécriture complète)
- Create: `tests/Feature/Admin/EventShowAdminTest.php` (4 tests)

- [ ] **Step 1: Write failing tests**

Create `tests/Feature/Admin/EventShowAdminTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventShowAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_renders_featured_image_when_present(): void
    {
        $admin = $this->makeAdmin();
        $event = Event::create([
            'organizer_id' => $admin->id,
            'title' => 'Conférence',
            'slug' => 'conf',
            'start_date' => now()->addWeek(),
            'featured_image' => 'events/images/cover.jpg',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/events/{$event->id}");

        $response->assertOk()
            ->assertSee('events/images/cover.jpg', escape: false);
    }

    public function test_show_renders_content_html_correctly(): void
    {
        $admin = $this->makeAdmin();
        $event = Event::create([
            'organizer_id' => $admin->id,
            'title' => 'HTML',
            'slug' => 'html',
            'start_date' => now()->addWeek(),
            'content' => '<p>Programme <strong>détaillé</strong></p>',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/events/{$event->id}");

        $response->assertOk()
            ->assertSee('<strong>détaillé</strong>', escape: false)
            ->assertDontSee('&lt;strong&gt;', escape: false);
    }

    public function test_show_displays_upcoming_badge_for_future_event(): void
    {
        $admin = $this->makeAdmin();
        $event = Event::create([
            'organizer_id' => $admin->id,
            'title' => 'Futur',
            'slug' => 'futur',
            'start_date' => now()->addWeek(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/events/{$event->id}");

        $response->assertOk()->assertSee('À venir');
    }

    public function test_show_displays_past_badge_for_past_event(): void
    {
        $admin = $this->makeAdmin();
        $event = Event::create([
            'organizer_id' => $admin->id,
            'title' => 'Passé',
            'slug' => 'passe',
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(7),
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/events/{$event->id}");

        $response->assertOk()->assertSee('Passé');
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }
}
```

- [ ] **Step 2: Run, expect failure**

`php artisan test --filter=EventShowAdminTest`
Expected: tests 1 and 2 fail (no image rendering, `nl2br(e())` bug). Tests 3 and 4 may pass partially.

- [ ] **Step 3: Replace `show.blade.php` content**

Replace the entire content of `resources/views/admin/events/show.blade.php` with:

```blade
@extends('layouts.admin')
@section('title', $event->title)
@section('breadcrumb')
    <a href="{{ route('admin.events.index') }}">Événements</a>
    <span>/</span>
    <span>{{ Str::limit($event->title, 30) }}</span>
@endsection

@push('styles')
<style>
    .article-content { line-height: 1.7; color: #1C2B27; font-size: 1rem; }
    .article-content p { margin: 0 0 1em; }
    .article-content h2 { font-size: 1.5rem; font-weight: 700; margin: 1.5em 0 0.5em; color: #16302B; }
    .article-content h3 { font-size: 1.25rem; font-weight: 700; margin: 1.2em 0 0.4em; color: #16302B; }
    .article-content ul, .article-content ol { margin: 0 0 1em 1.5em; padding: 0; }
    .article-content li { margin-bottom: 0.25em; }
    .article-content blockquote { border-left: 3px solid #85B79D; padding: 0.25em 0 0.25em 1em; color: #68756F; margin: 1em 0; font-style: italic; }
    .article-content a { color: #356B8A; text-decoration: underline; }
    .article-content strong { font-weight: 700; }
    .article-content em { font-style: italic; }
</style>
@endpush

@section('content')
    {{-- Header actions --}}
    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-secondary">Modifier</a>
        @if($event->status === 'published')
            <a href="{{ route('hub.events.show', $event) }}" target="_blank" class="btn btn-primary">Voir côté public ↗</a>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        {{-- SIDEBAR MÉTA --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">Informations</h3></div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    @if($event->status === 'published')
                        <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1rem;">Publié</span>
                    @else
                        <span class="badge badge-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">Brouillon</span>
                    @endif
                    @if($event->isUpcoming())
                        <span class="badge badge-info" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-left: 0.5rem;">À venir</span>
                    @elseif($event->isPast())
                        <span class="badge badge-warning" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-left: 0.5rem;">Passé</span>
                    @else
                        <span class="badge badge-warning" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-left: 0.5rem;">En cours</span>
                    @endif
                </div>

                @if($event->event_type)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Type</div>
                        <div>{{ $event->event_type }}</div>
                    </div>
                @endif

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Date début</div>
                    <div>{{ $event->start_date->format('d/m/Y H:i') }}</div>
                </div>

                @if($event->end_date)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Date fin</div>
                        <div>{{ $event->end_date->format('d/m/Y H:i') }}</div>
                    </div>
                @endif

                @if($event->location_name)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Lieu</div>
                        <div>{{ $event->location_name }}</div>
                        @if($event->location_city)
                            <div style="color: #6b7280; font-size: 0.875rem;">{{ $event->location_city }}</div>
                        @endif
                    </div>
                @endif

                @if($event->organizer)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Organisateur</div>
                        <div>{{ $event->organizer->name }}</div>
                    </div>
                @endif

                @if($event->price !== null)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Tarif</div>
                        <div>
                            @if((float) $event->price > 0)
                                {{ number_format($event->price, 2, ',', ' ') }} €
                            @else
                                Gratuit
                            @endif
                        </div>
                    </div>
                @endif

                @if($event->published_at)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Publié le</div>
                        <div>{{ $event->published_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- BODY --}}
        <div class="card">
            <div class="card-body">
                @if($event->featured_image)
                    <img src="{{ Storage::url($event->featured_image) }}" alt="" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                @endif

                <h1 style="font-size: 1.875rem; font-weight: 700; color: #16302B; margin: 0 0 1rem;">{{ $event->title }}</h1>

                {{-- Date+lieu en bandeau --}}
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; padding: 0.75rem 1rem; background: #f9fafb; border-radius: 0.5rem; font-size: 0.95rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i data-lucide="calendar" style="width: 18px; height: 18px; color: #356B8A;"></i>
                        @php
                            $start = $event->start_date;
                            $end = $event->end_date;
                            $sameDay = $end && $start->isSameDay($end);
                        @endphp
                        @if($end && !$sameDay)
                            <span>{{ $start->locale('fr')->isoFormat('LL') }} → {{ $end->locale('fr')->isoFormat('LL') }}</span>
                        @elseif($end && $sameDay)
                            <span>{{ $start->locale('fr')->isoFormat('LL') }} · {{ $start->format('H\hi') }} → {{ $end->format('H\hi') }}</span>
                        @else
                            <span>{{ $start->locale('fr')->isoFormat('LL') }} · {{ $start->format('H\hi') }}</span>
                        @endif
                    </div>
                    @if($event->location_name || $event->location_address || $event->location_city)
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="map-pin" style="width: 18px; height: 18px; color: #356B8A;"></i>
                            @php
                                $addr = trim(implode(', ', array_filter([$event->location_name, $event->location_address, $event->location_city])));
                                $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($addr);
                            @endphp
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" style="color: #356B8A; text-decoration: underline;">{{ $addr }}</a>
                        </div>
                    @endif
                </div>

                @if($event->description)
                    <p style="font-size: 1.05rem; font-style: italic; color: #6b7280; margin-bottom: 1.5rem;">{{ $event->description }}</p>
                @endif

                @if($event->content)
                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 1.5rem 0;">
                    <div class="article-content">
                        {!! $event->content !!}
                    </div>
                @endif

                @if($event->registration_required || $event->registration_url || $event->max_participants)
                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 2rem 0;">
                    <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                        <h3 style="margin: 0 0 0.75rem; font-size: 1.125rem; font-weight: 600;">Inscription</h3>
                        @if($event->max_participants)
                            <p style="margin: 0 0 0.5rem; color: #4b5563;">Maximum {{ $event->max_participants }} participants</p>
                        @endif
                        @if($event->registration_required && ! $event->registration_url)
                            <p style="margin: 0; color: #4b5563;">Inscription obligatoire (procédure à préciser).</p>
                        @endif
                        @if($event->registration_url)
                            <a href="{{ $event->registration_url }}" target="_blank" rel="noopener" class="btn btn-primary" style="margin-top: 0.5rem;">S'inscrire ↗</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
```

- [ ] **Step 4: Run tests, expect pass**

`php artisan test --filter=EventShowAdminTest`
Expected: 4 passing.

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/events/show.blade.php tests/Feature/Admin/EventShowAdminTest.php
git commit -m "feat(admin): refonte page show event (image cover, HTML rendu, sidebar méta, dates+lieu)"
```

---

## Task 3 — Event form : input image + Quill + enctype

**Files:**
- Modify: `resources/views/admin/events/_form.blade.php` (ajout image upload, Quill remplace textarea)
- Modify: `resources/views/admin/events/edit.blade.php` (enctype si absent)
- Modify: `resources/views/admin/events/create.blade.php` (enctype si absent)

- [ ] **Step 1: Read current `_form.blade.php` and locate the content textarea**

`cat resources/views/admin/events/_form.blade.php` (or use Read tool). Find the line where `<textarea name="content">` is defined (around line 20 per the audit).

- [ ] **Step 2: Replace the content textarea with Quill block**

In `resources/views/admin/events/_form.blade.php`, replace the `<textarea name="content">...</textarea>` block (and its surrounding `.form-group`) with:

```blade
<div class="form-group">
    <label class="form-label" for="content">Contenu détaillé</label>
    <input type="hidden" name="content" id="content-input" value="{{ old('content', $event->content ?? '') }}">
    <div id="editor" style="min-height: 300px; background: white; border: 1px solid #d1d5db; border-radius: 8px;"></div>
    @error('content')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-container { font-family: 'Inter', sans-serif; font-size: 15px; line-height: 1.7; }
    .ql-editor { min-height: 280px; color: #1C2B27; }
    .ql-editor h2 { font-size: 24px; font-weight: 700; margin: 1.2em 0 0.5em; }
    .ql-editor h3 { font-size: 20px; font-weight: 700; margin: 1em 0 0.4em; }
    .ql-editor blockquote { border-left: 3px solid #85B79D; padding-left: 16px; color: #68756F; }
    .ql-editor a { color: #356B8A; }
    .ql-toolbar { border-radius: 8px 8px 0 0; border-color: #d1d5db; background: #fafafa; }
    .ql-container { border-radius: 0 0 8px 8px; border-color: #d1d5db; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Rédigez le contenu détaillé de l\'événement...',
            modules: {
                toolbar: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['blockquote', 'link'],
                    [{ align: [] }],
                    ['clean']
                ]
            }
        });
        const existingContent = document.getElementById('content-input').value;
        if (existingContent) {
            quill.root.innerHTML = existingContent;
        }
        quill.root.closest('form').addEventListener('submit', function() {
            document.getElementById('content-input').value = quill.root.innerHTML;
        });
    });
</script>
@endpush
```

- [ ] **Step 3: Add image upload field**

Still in `_form.blade.php`, in the right column (the `<div>` after the closing `</div>` of the left column with title/slug/description/content/location), add this `.form-group` block. Place it before the existing "Inscription" block. If unclear, add it at the very end of the right column just before the closing `</div>` of the column.

```blade
<div class="form-group">
    <label class="form-label" for="featured_image">Image de couverture</label>
    @if(isset($event) && $event->featured_image)
        <div style="margin-bottom: 0.5rem;">
            <img src="{{ Storage::url($event->featured_image) }}" alt="Image actuelle" style="max-height: 120px; border-radius: 8px; border: 1px solid #e5e7eb;">
        </div>
    @endif
    <input type="file" name="featured_image" id="featured_image" class="form-input" accept="image/*">
    <p style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">JPG, PNG ou WebP. Max 5 Mo.</p>
    @error('featured_image')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
</div>
```

- [ ] **Step 4: Ensure enctype on edit.blade.php and create.blade.php**

Read `resources/views/admin/events/edit.blade.php` and `resources/views/admin/events/create.blade.php`. If the `<form>` tag does NOT have `enctype="multipart/form-data"`, add it. Example pattern:

Before:
```blade
<form action="{{ route('admin.events.update', $event) }}" method="POST">
```

After:
```blade
<form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
```

- [ ] **Step 5: Smoke test that the page still renders**

`php artisan view:clear`
`php -d memory_limit=256M artisan view:cache 2>&1 | tail -3`
Expected: `Blade templates cached successfully.` (no parse error from the new Quill block).

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/events/_form.blade.php resources/views/admin/events/edit.blade.php resources/views/admin/events/create.blade.php
git commit -m "feat(admin): events form ajoute Quill + input image avec enctype multipart"
```

---

## Task 4 — EventController : upload image dans store/update

**Files:**
- Modify: `app/Http/Controllers/Admin/EventController.php` (méthodes `store` et `update`)
- Create: `tests/Feature/Admin/EventFormImageTest.php` (3 tests)

- [ ] **Step 1: Write failing tests**

Create `tests/Feature/Admin/EventFormImageTest.php`:

```php
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
```

- [ ] **Step 2: Run, expect failure**

`php artisan test --filter=EventFormImageTest`
Expected: 3 fail (no validation rule for `featured_image`, no upload logic in controller).

- [ ] **Step 3: Update `EventController::store()`**

Read the current `store()` method first. Find the `$validated = $request->validate([...]);` block. Add this rule:

```php
'featured_image' => 'nullable|image|max:5120',
```

After the `validate()` call and before `Event::create($validated)` (or whatever the existing variable is), add:

```php
if ($request->hasFile('featured_image')) {
    $validated['featured_image'] = $request->file('featured_image')
        ->store('events/images', 'public');
}
```

- [ ] **Step 4: Update `EventController::update()`**

Same validation rule addition. After `validate()` and before `$event->update($validated)`:

```php
if ($request->hasFile('featured_image')) {
    if ($event->featured_image) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($event->featured_image);
    }
    $validated['featured_image'] = $request->file('featured_image')
        ->store('events/images', 'public');
}
```

- [ ] **Step 5: Run tests, expect pass**

`php artisan test --filter=EventFormImageTest`
Expected: 3 passing.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/EventController.php tests/Feature/Admin/EventFormImageTest.php
git commit -m "feat(admin): EventController gère l'upload de featured_image (validation + delete ancien)"
```

---

## Task 5 — Smoke test + merge

**Files:** none (verification only)

- [ ] **Step 1: Run full suite**

`php -d memory_limit=512M artisan test 2>&1 | tail -4`
Expected: 472 passing (461 previous + 11 new). Investigate any regression.

- [ ] **Step 2: View cache clear**

`php artisan view:clear`

- [ ] **Step 3: Manual smoke**

Start server: `php artisan serve`

Visit:
- `http://localhost:8000/extranet/articles/1` (or any article id) — image visible si présente, contenu HTML rendu correctement, sidebar à gauche avec stats.
- `http://localhost:8000/extranet/events/1` (or any event id) — image visible, dates+lieu en bandeau, sidebar méta.
- `http://localhost:8000/extranet/events/create` — formulaire avec champ image upload + éditeur Quill (toolbar visible).
- Créer un événement avec image → image visible sur show admin après save.
- Éditer un événement existant et changer son image → ancienne image remplacée.

- [ ] **Step 4: Merge feature branch into main**

```bash
git checkout main
git merge --no-ff feature/admin-articles-events-show -m "Merge branch 'feature/admin-articles-events-show'

Refonte des pages admin show pour articles et events:
- Layout 1fr 2fr (sidebar méta gauche + body riche droite)
- Image cover affichée (correctif bug)
- Contenu HTML rendu correctement (correctif bug nl2br(e()))
- Events form aligné sur articles (Quill + input image + enctype)
- 11 nouveaux tests, suite full verte"
git push origin main
git branch -d feature/admin-articles-events-show
```

---

## Self-review

**Spec coverage:**

| Spec section | Tasks |
|---|---|
| Show admin Article — sidebar méta complète + image + HTML rendu + bouton Voir public + bloc validation | Task 1 |
| Show admin Event — sidebar méta + image + dates+lieu + content + bloc inscription | Task 2 |
| Form events — input image | Task 3 (Step 3) |
| Form events — Quill remplace textarea | Task 3 (Step 2) |
| edit/create events — enctype multipart | Task 3 (Step 4) |
| EventController — validation featured_image | Task 4 (Step 3, 4) |
| EventController — store/replace logic + delete ancien | Task 4 (Step 3, 4) |
| Tests show admin article (4) | Task 1 |
| Tests show admin event (4) | Task 2 |
| Tests form image events (3) | Task 4 |

Toutes les exigences du spec sont couvertes.

**Type consistency:**
- `Storage::url($model->featured_image)` cohérent partout (Tasks 1, 2, 3).
- `class="article-content"` même nom CSS dans Tasks 1 et 2 (mêmes styles dupliqués — acceptable car local à chaque show, pas de fichier CSS partagé).
- `enctype="multipart/form-data"` requis pour le file input (Task 3 Step 4).
- `events/images/` chemin de stockage cohérent (Task 4 Step 3, 4 + Task 3 Step 3).
- Quill v2 CDN même version partout (cohérent avec articles existant).

**Placeholders:** none. Tous les blocs de code sont complets.

**Note importante** : la duplication du bloc `<style>.article-content { ... }</style>` entre Tasks 1 et 2 est intentionnelle pour ne pas créer un fichier CSS partagé inutile (admin.css est déjà gros). Si un futur design veut une 3e instance, à ce moment-là extraire dans une classe Tailwind ou un partial Blade.
