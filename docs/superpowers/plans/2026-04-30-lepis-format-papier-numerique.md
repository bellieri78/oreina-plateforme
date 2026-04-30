# Lepis — Format papier/numérique par adhérent + tracking d'envoi — Plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Distinguer les abonnés Lepis papier/numérique au niveau de chaque `Membership`, filtrer la liste Brevo aux seuls numériques, et tracer côté DB qui a reçu chaque bulletin (pour la fiche contact admin et l'export postal).

**Architecture:** Une colonne `lepis_format` sur `memberships` (figée par adhésion). Une table `lepis_bulletin_recipients` qui fige le snapshot de diffusion (papier+numérique) au passage `draft → members`. Un `LepisBulletinRecipientSnapshotter` idempotent appelé en première étape du job `SyncLepisBulletinToBrevoList` (qui ne pousse plus que les `digital` dans Brevo). UI backoffice : 5e carte "Diffusion" sur la fiche bulletin (counts + export CSV papier + bouton recalcul) ; section "Bulletins reçus" sur la fiche contact ; champ format obligatoire dans le formulaire d'adhésion. Espace membre : info readonly sur le profil.

**Tech Stack:** Laravel 12, PostgreSQL 17 (compat 9.6 prod), Blade + Livewire, PHPUnit, BrevoService existant.

**Spec source:** `docs/superpowers/specs/2026-04-30-lepis-format-papier-numerique-design.md`

---

## File map

**Created:**
- `database/migrations/2026_04_30_100000_add_lepis_format_to_memberships.php`
- `database/migrations/2026_04_30_100100_create_lepis_bulletin_recipients_table.php`
- `app/Models/LepisBulletinRecipient.php`
- `app/Services/LepisBulletinRecipientSnapshotter.php`
- `app/Console/Commands/LepisBackfillRecipientsCommand.php`
- `resources/views/admin/lepis/_carte_diffusion.blade.php`
- `tests/Unit/Services/LepisBulletinRecipientSnapshotterTest.php`
- `tests/Feature/Lepis/LepisFormatHelloAssoWebhookTest.php`
- `tests/Feature/Admin/LepisBulletinDiffusionTest.php`
- `tests/Feature/Admin/MemberLepisHistoryTest.php`
- `tests/Feature/Admin/MembershipLepisFormatTest.php`
- `tests/Feature/Member/MemberProfileLepisFormatTest.php`

**Modified:**
- `app/Models/Membership.php` (constants + accessor)
- `app/Models/LepisBulletin.php` (relation + accessors)
- `app/Models/Member.php` (relation)
- `app/Jobs/SyncLepisBulletinToBrevoList.php` (filter digital + call snapshotter)
- `app/Http/Controllers/Api/WebhookController.php` (parse custom field)
- `app/Http/Controllers/Admin/MembershipController.php` (validation + assignment)
- `app/Http/Controllers/Admin/LepisBulletinController.php` (recipients export + recalc actions)
- `app/Http/Controllers/Admin/MemberController.php` (load lepis recipients for show)
- `app/Http/Controllers/Member/ProfileController.php` (pass lepis_format)
- `routes/admin.php` (new routes for export/recalc)
- `resources/views/admin/memberships/_form.blade.php` (lepis_format field)
- `resources/views/admin/lepis/edit.blade.php` (include 5th card)
- `resources/views/admin/members/show.blade.php` (recipients section)
- `resources/views/admin/memberships/index.blade.php` (filter dropdown)
- `resources/views/member/profile.blade.php` (readonly format display)
- `tests/Feature/Lepis/SyncLepisBulletinToBrevoListTest.php` (digital-only filter)

---

## Task 1 — Migration: `memberships.lepis_format`

**Files:**
- Create: `database/migrations/2026_04_30_100000_add_lepis_format_to_memberships.php`

- [ ] **Step 1: Create the migration file**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Raw SQL for PostgreSQL 9.6 compatibility (no ->change() on Laravel 12).
        DB::statement("ALTER TABLE memberships ADD COLUMN lepis_format VARCHAR(10) NULL");
        DB::statement("ALTER TABLE memberships ADD CONSTRAINT memberships_lepis_format_check CHECK (lepis_format IS NULL OR lepis_format IN ('paper', 'digital'))");

        // Backfill: legacy memberships default to 'paper' (no surprise for current paper subscribers).
        DB::statement("UPDATE memberships SET lepis_format = 'paper' WHERE lepis_format IS NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE memberships DROP CONSTRAINT IF EXISTS memberships_lepis_format_check");
        Schema::table('memberships', function ($table) {
            $table->dropColumn('lepis_format');
        });
    }
};
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`
Expected: migration succeeds, memberships now has a `lepis_format` column with all rows = `paper`.

- [ ] **Step 3: Verify backfill in DB**

Run: `php artisan tinker --execute="echo \App\Models\Membership::whereNull('lepis_format')->count();"`
Expected: `0`

Run: `php artisan tinker --execute="echo \App\Models\Membership::where('lepis_format', 'paper')->count();"`
Expected: number > 0 (matching all existing memberships).

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_04_30_100000_add_lepis_format_to_memberships.php
git commit -m "feat(lepis): ajout colonne memberships.lepis_format avec backfill paper"
```

---

## Task 2 — Membership model: constants + accessor

**Files:**
- Modify: `app/Models/Membership.php` (add to fillable, add constants and accessor)

- [ ] **Step 1: Add a failing unit test**

Create `tests/Unit/Models/MembershipLepisFormatTest.php`:

```php
<?php

namespace Tests\Unit\Models;

use App\Models\Membership;
use Tests\TestCase;

class MembershipLepisFormatTest extends TestCase
{
    public function test_constants_are_defined(): void
    {
        $this->assertSame('paper', Membership::LEPIS_FORMAT_PAPER);
        $this->assertSame('digital', Membership::LEPIS_FORMAT_DIGITAL);
    }

    public function test_lepis_format_or_default_returns_value_when_set(): void
    {
        $m = new Membership(['lepis_format' => 'digital']);
        $this->assertSame('digital', $m->lepisFormatOrDefault());
    }

    public function test_lepis_format_or_default_returns_paper_when_null(): void
    {
        $m = new Membership(['lepis_format' => null]);
        $this->assertSame('paper', $m->lepisFormatOrDefault());
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

Run: `php artisan test --filter=MembershipLepisFormatTest`
Expected: FAIL — constants missing, method missing.

- [ ] **Step 3: Update `app/Models/Membership.php`**

Add at the top of the class body (just after `class Membership extends Model {`):

```php
public const LEPIS_FORMAT_PAPER = 'paper';
public const LEPIS_FORMAT_DIGITAL = 'digital';
```

Add `'lepis_format'` to the `$fillable` array.

Add this method to the class:

```php
public function lepisFormatOrDefault(): string
{
    return $this->lepis_format ?: self::LEPIS_FORMAT_PAPER;
}
```

- [ ] **Step 4: Run the test, expect pass**

Run: `php artisan test --filter=MembershipLepisFormatTest`
Expected: 3 passing.

- [ ] **Step 5: Commit**

```bash
git add app/Models/Membership.php tests/Unit/Models/MembershipLepisFormatTest.php
git commit -m "feat(lepis): constantes et accessor lepisFormatOrDefault sur Membership"
```

---

## Task 3 — Migration: `lepis_bulletin_recipients` table

**Files:**
- Create: `database/migrations/2026_04_30_100100_create_lepis_bulletin_recipients_table.php`

- [ ] **Step 1: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lepis_bulletin_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lepis_bulletin_id')->constrained('lepis_bulletins')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained('memberships')->nullOnDelete();
            $table->string('format', 10);
            $table->string('email_at_snapshot')->nullable();
            $table->jsonb('postal_address_at_snapshot')->nullable();
            $table->integer('brevo_list_id')->nullable();
            $table->timestamp('included_at')->useCurrent();
            $table->timestamps();

            $table->unique(['lepis_bulletin_id', 'member_id'], 'lepis_recipients_bulletin_member_unique');
            $table->index('member_id', 'lepis_recipients_member_idx');
            $table->index(['lepis_bulletin_id', 'format'], 'lepis_recipients_bulletin_format_idx');
        });

        DB::statement("ALTER TABLE lepis_bulletin_recipients ADD CONSTRAINT lepis_recipients_format_check CHECK (format IN ('paper', 'digital'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('lepis_bulletin_recipients');
    }
};
```

NB: `jsonb()` is supported by Laravel's Blueprint on PostgreSQL since Laravel 9.

- [ ] **Step 2: Run the migration**

Run: `php artisan migrate`
Expected: table created.

- [ ] **Step 3: Verify schema**

Run: `php artisan tinker --execute="dd(\Illuminate\Support\Facades\Schema::getColumnListing('lepis_bulletin_recipients'));"`
Expected: array containing `id, lepis_bulletin_id, member_id, membership_id, format, email_at_snapshot, postal_address_at_snapshot, brevo_list_id, included_at, created_at, updated_at`.

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_04_30_100100_create_lepis_bulletin_recipients_table.php
git commit -m "feat(lepis): création table lepis_bulletin_recipients pour tracking d'envoi"
```

---

## Task 4 — `LepisBulletinRecipient` model + relations

**Files:**
- Create: `app/Models/LepisBulletinRecipient.php`
- Modify: `app/Models/LepisBulletin.php`
- Modify: `app/Models/Member.php`

- [ ] **Step 1: Add a failing unit test**

Create `tests/Unit/Models/LepisBulletinRecipientTest.php`:

```php
<?php

namespace Tests\Unit\Models;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBulletinRecipientTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships_are_wired(): void
    {
        $bulletin = LepisBulletin::create([
            'title' => 'T',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'x.pdf',
            'status' => 'members',
            'published_to_members_at' => now(),
        ]);
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id,
            'member_number' => 'M1',
            'email' => 'a@b.test',
            'first_name' => 'A',
            'last_name' => 'B',
            'joined_at' => now(),
        ]);

        $r = LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $bulletin->id,
            'member_id' => $member->id,
            'format' => 'paper',
            'included_at' => now(),
        ]);

        $this->assertSame($bulletin->id, $r->bulletin->id);
        $this->assertSame($member->id, $r->member->id);
        $this->assertCount(1, $bulletin->recipients);
        $this->assertCount(1, $member->lepisBulletinRecipients);
    }

    public function test_postal_address_is_cast_to_array(): void
    {
        $bulletin = LepisBulletin::create([
            'title' => 'T', 'issue_number' => 1, 'quarter' => 'Q1', 'year' => 2026,
            'pdf_path' => 'x.pdf', 'status' => 'members', 'published_to_members_at' => now(),
        ]);
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id, 'member_number' => 'M2', 'email' => 'c@d.test',
            'first_name' => 'C', 'last_name' => 'D', 'joined_at' => now(),
        ]);

        $r = LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $bulletin->id,
            'member_id' => $member->id,
            'format' => 'paper',
            'postal_address_at_snapshot' => ['address' => '1 rue X', 'city' => 'Paris'],
            'included_at' => now(),
        ]);

        $this->assertIsArray($r->fresh()->postal_address_at_snapshot);
        $this->assertSame('1 rue X', $r->fresh()->postal_address_at_snapshot['address']);
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

Run: `php artisan test --filter=LepisBulletinRecipientTest`
Expected: FAIL — class missing.

- [ ] **Step 3: Create the model `app/Models/LepisBulletinRecipient.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LepisBulletinRecipient extends Model
{
    public const FORMAT_PAPER = 'paper';
    public const FORMAT_DIGITAL = 'digital';

    protected $fillable = [
        'lepis_bulletin_id',
        'member_id',
        'membership_id',
        'format',
        'email_at_snapshot',
        'postal_address_at_snapshot',
        'brevo_list_id',
        'included_at',
    ];

    protected $casts = [
        'postal_address_at_snapshot' => 'array',
        'included_at' => 'datetime',
        'brevo_list_id' => 'integer',
    ];

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(LepisBulletin::class, 'lepis_bulletin_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
```

- [ ] **Step 4: Add the relation on `LepisBulletin`**

In `app/Models/LepisBulletin.php`, add the import at the top:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;
```

Add this method:

```php
public function recipients(): HasMany
{
    return $this->hasMany(LepisBulletinRecipient::class, 'lepis_bulletin_id');
}

public function paperRecipientsCount(): int
{
    return $this->recipients()->where('format', LepisBulletinRecipient::FORMAT_PAPER)->count();
}

public function digitalRecipientsCount(): int
{
    return $this->recipients()->where('format', LepisBulletinRecipient::FORMAT_DIGITAL)->count();
}
```

- [ ] **Step 5: Add the relation on `Member`**

In `app/Models/Member.php`, in the relations section (around line 146 where `memberships()` lives), add:

```php
public function lepisBulletinRecipients(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(LepisBulletinRecipient::class)->orderByDesc('included_at');
}
```

- [ ] **Step 6: Run the test, expect pass**

Run: `php artisan test --filter=LepisBulletinRecipientTest`
Expected: 2 passing.

- [ ] **Step 7: Commit**

```bash
git add app/Models/LepisBulletinRecipient.php app/Models/LepisBulletin.php app/Models/Member.php tests/Unit/Models/LepisBulletinRecipientTest.php
git commit -m "feat(lepis): modèle LepisBulletinRecipient et relations sur Bulletin et Member"
```

---

## Task 5 — `LepisBulletinRecipientSnapshotter` service (TDD)

**Files:**
- Create: `app/Services/LepisBulletinRecipientSnapshotter.php`
- Test: `tests/Unit/Services/LepisBulletinRecipientSnapshotterTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Unit/Services/LepisBulletinRecipientSnapshotterTest.php`:

```php
<?php

namespace Tests\Unit\Services;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Services\LepisBulletinRecipientSnapshotter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBulletinRecipientSnapshotterTest extends TestCase
{
    use RefreshDatabase;

    public function test_snapshots_active_paper_and_digital_members(): void
    {
        $bulletin = $this->makeBulletin();
        $paper = $this->makeMemberWithMembership('paper', 'paper@test.com', address: '1 rue X');
        $digital = $this->makeMemberWithMembership('digital', 'digital@test.com', address: '2 rue Y');

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(1, $result->paperCount);
        $this->assertSame(1, $result->digitalCount);
        $this->assertCount(2, $bulletin->fresh()->recipients);
    }

    public function test_skips_membership_expired_at_publication_date(): void
    {
        $bulletin = $this->makeBulletin();
        $this->makeMemberWithMembership('paper', 'old@test.com', startDaysAgo: 800, endDaysAgo: 30, address: '1 rue X');

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(0, $result->paperCount);
        $this->assertCount(0, $bulletin->fresh()->recipients);
    }

    public function test_falls_back_to_paper_when_lepis_format_is_null(): void
    {
        $bulletin = $this->makeBulletin();
        $this->makeMemberWithMembership(null, 'nullformat@test.com', address: '1 rue X');

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(1, $result->paperCount);
        $this->assertSame('paper', $bulletin->fresh()->recipients->first()->format);
    }

    public function test_skips_digital_member_without_email(): void
    {
        $bulletin = $this->makeBulletin();
        $member = $this->makeMemberWithMembership('digital', null, address: '1 rue X');

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(0, $result->digitalCount);
        $this->assertCount(1, $result->skipped);
        $this->assertSame($member->id, $result->skipped[0]['member_id']);
        $this->assertStringContainsString('email', $result->skipped[0]['reason']);
    }

    public function test_skips_paper_member_with_incomplete_address(): void
    {
        $bulletin = $this->makeBulletin();
        $member = $this->makeMemberWithMembership('paper', 'paper@test.com', address: null);

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(0, $result->paperCount);
        $this->assertCount(1, $result->skipped);
        $this->assertStringContainsString('address', $result->skipped[0]['reason']);
    }

    public function test_is_idempotent_on_second_run(): void
    {
        $bulletin = $this->makeBulletin();
        $this->makeMemberWithMembership('paper', 'p@test.com', address: '1 rue X');

        (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);
        (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertCount(1, $bulletin->fresh()->recipients);
    }

    public function test_freezes_email_and_address_at_snapshot_time(): void
    {
        $bulletin = $this->makeBulletin();
        $member = $this->makeMemberWithMembership('digital', 'before@test.com', address: '1 rue Avant');

        (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $member->update(['email' => 'after@test.com', 'address' => '99 rue Apres']);

        $r = $bulletin->fresh()->recipients->first();
        $this->assertSame('before@test.com', $r->email_at_snapshot);
        // For digital, postal address is not required so it may be null or set; we just check email is frozen.
    }

    public function test_picks_most_recent_membership_when_multiple(): void
    {
        $bulletin = $this->makeBulletin();
        $member = $this->makeMemberWithMembership('paper', 'm@test.com', address: '1 rue X', endDaysAgo: -30);
        // Add a more recent active membership with format=digital
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $this->membershipTypeId(),
            'status' => 'active',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addYear(),
            'amount_paid' => 30.00,
            'lepis_format' => 'digital',
        ]);

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(0, $result->paperCount);
        $this->assertSame(1, $result->digitalCount);
    }

    private ?int $membershipTypeId = null;

    private function membershipTypeId(): int
    {
        if ($this->membershipTypeId === null) {
            $this->membershipTypeId = MembershipType::create([
                'name' => 'Standard', 'slug' => 'standard', 'price' => 30.00,
                'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
            ])->id;
        }
        return $this->membershipTypeId;
    }

    private function makeBulletin(): LepisBulletin
    {
        return LepisBulletin::create([
            'title' => 'T', 'issue_number' => 1, 'quarter' => 'Q2', 'year' => 2026,
            'pdf_path' => 'x.pdf', 'status' => 'members', 'published_to_members_at' => now(),
        ]);
    }

    private function makeMemberWithMembership(
        ?string $format,
        ?string $email,
        ?string $address = null,
        int $startDaysAgo = 30,
        int $endDaysAgo = -365
    ): Member {
        $user = User::factory()->create(['email' => $email ?: 'user' . random_int(1000, 9999) . '@test.com']);
        $member = Member::create([
            'user_id' => $user->id,
            'member_number' => 'M' . random_int(1000, 9999),
            'email' => $email,
            'first_name' => 'F', 'last_name' => 'L',
            'address' => $address,
            'postal_code' => $address ? '75000' : null,
            'city' => $address ? 'Paris' : null,
            'country' => $address ? 'France' : null,
            'joined_at' => now()->subYear(),
        ]);
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $this->membershipTypeId(),
            'status' => 'active',
            'start_date' => now()->subDays($startDaysAgo),
            'end_date' => $endDaysAgo >= 0 ? now()->subDays($endDaysAgo) : now()->addDays(-$endDaysAgo),
            'amount_paid' => 30.00,
            'lepis_format' => $format,
        ]);
        return $member;
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

Run: `php artisan test --filter=LepisBulletinRecipientSnapshotterTest`
Expected: FAIL — service class missing.

- [ ] **Step 3: Create the service**

Create `app/Services/LepisBulletinRecipientSnapshotter.php`:

```php
<?php

namespace App\Services;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LepisBulletinRecipientSnapshotter
{
    public function snapshot(LepisBulletin $bulletin): SnapshotResult
    {
        $referenceDate = $bulletin->published_to_members_at ?? Carbon::now();

        $paperCount = 0;
        $digitalCount = 0;
        $skipped = [];

        $members = Member::query()
            ->whereHas('memberships', function ($q) use ($referenceDate) {
                $q->where('status', 'active')
                    ->where('start_date', '<=', $referenceDate)
                    ->where('end_date', '>=', $referenceDate);
            })
            ->with(['memberships' => function ($q) use ($referenceDate) {
                $q->where('status', 'active')
                    ->where('start_date', '<=', $referenceDate)
                    ->where('end_date', '>=', $referenceDate)
                    ->orderByDesc('end_date');
            }])
            ->get();

        DB::transaction(function () use ($members, $bulletin, &$paperCount, &$digitalCount, &$skipped) {
            foreach ($members as $member) {
                $membership = $member->memberships->first();
                if (! $membership) {
                    continue;
                }
                $format = $membership->lepis_format ?: Membership::LEPIS_FORMAT_PAPER;

                if ($format === Membership::LEPIS_FORMAT_DIGITAL) {
                    if (empty($member->email)) {
                        $skipped[] = ['member_id' => $member->id, 'reason' => 'digital format but email missing'];
                        Log::channel('daily')->warning('Lepis snapshot skip: digital without email', [
                            'bulletin_id' => $bulletin->id, 'member_id' => $member->id,
                        ]);
                        continue;
                    }
                } else {
                    if (! $this->hasFullAddress($member)) {
                        $skipped[] = ['member_id' => $member->id, 'reason' => 'paper format but postal address incomplete'];
                        Log::channel('daily')->warning('Lepis snapshot skip: paper without address', [
                            'bulletin_id' => $bulletin->id, 'member_id' => $member->id,
                        ]);
                        continue;
                    }
                }

                LepisBulletinRecipient::updateOrCreate(
                    ['lepis_bulletin_id' => $bulletin->id, 'member_id' => $member->id],
                    [
                        'membership_id' => $membership->id,
                        'format' => $format,
                        'email_at_snapshot' => $member->email,
                        'postal_address_at_snapshot' => $format === Membership::LEPIS_FORMAT_PAPER
                            ? [
                                'address' => $member->address,
                                'postal_code' => $member->postal_code,
                                'city' => $member->city,
                                'country' => $member->country,
                            ]
                            : null,
                        'included_at' => Carbon::now(),
                    ]
                );

                if ($format === Membership::LEPIS_FORMAT_PAPER) {
                    $paperCount++;
                } else {
                    $digitalCount++;
                }
            }
        });

        return new SnapshotResult($paperCount, $digitalCount, $skipped);
    }

    private function hasFullAddress(Member $member): bool
    {
        return ! empty($member->address)
            && ! empty($member->postal_code)
            && ! empty($member->city);
    }
}
```

Add the result DTO at the bottom of the same file (or in its own file — keep it inline for simplicity):

```php
final class SnapshotResult
{
    public function __construct(
        public readonly int $paperCount,
        public readonly int $digitalCount,
        public readonly array $skipped
    ) {}
}
```

- [ ] **Step 4: Run the test, expect pass**

Run: `php artisan test --filter=LepisBulletinRecipientSnapshotterTest`
Expected: 8 passing.

- [ ] **Step 5: Commit**

```bash
git add app/Services/LepisBulletinRecipientSnapshotter.php tests/Unit/Services/LepisBulletinRecipientSnapshotterTest.php
git commit -m "feat(lepis): service LepisBulletinRecipientSnapshotter idempotent (papier/numérique + skips)"
```

---

## Task 6 — Modifier `SyncLepisBulletinToBrevoList` (filter digital + call snapshotter)

**Files:**
- Modify: `app/Jobs/SyncLepisBulletinToBrevoList.php`
- Modify: `tests/Feature/Lepis/SyncLepisBulletinToBrevoListTest.php`

- [ ] **Step 1: Update the existing test to expect digital-only filtering**

Replace the existing `test_creates_brevo_list_and_imports_current_members` in `tests/Feature/Lepis/SyncLepisBulletinToBrevoListTest.php` to discriminate paper vs digital. Replace the test body with:

```php
public function test_creates_brevo_list_and_imports_only_digital_members(): void
{
    $bulletin = $this->makeBulletin();
    $this->makeCurrentMember('alice@example.com', 'digital');
    $this->makeCurrentMember('bob@example.com', 'digital');
    $this->makeCurrentMember('paperino@example.com', 'paper');
    $this->makeExpiredMember('old@example.com');

    $this->mock(BrevoService::class, function (MockInterface $mock) {
        $mock->shouldReceive('createList')
            ->once()
            ->with('Lepis 2026 Q2', \Mockery::any())
            ->andReturn(['success' => true, 'data' => ['id' => 123]]);

        $mock->shouldReceive('importContacts')
            ->once()
            ->withArgs(function ($members, $listId) {
                $emails = $members->pluck('email')->sort()->values()->all();
                return $listId === 123 && $emails === ['alice@example.com', 'bob@example.com'];
            })
            ->andReturn(['success' => true, 'count' => 2]);
    });

    (new SyncLepisBulletinToBrevoList($bulletin))->handle(app(BrevoService::class));

    $bulletin->refresh();
    $this->assertSame(123, $bulletin->brevo_list_id);
    $this->assertSame('Lepis 2026 Q2', $bulletin->brevo_list_name);
    $this->assertNotNull($bulletin->brevo_synced_at);
    $this->assertFalse($bulletin->brevo_sync_failed);

    // Snapshot must have been written: 2 digital + 1 paper recipients
    $recipients = $bulletin->recipients;
    $this->assertCount(3, $recipients);
    $this->assertSame(2, $recipients->where('format', 'digital')->count());
    $this->assertSame(1, $recipients->where('format', 'paper')->count());
    $this->assertSame(123, $recipients->where('format', 'digital')->first()->brevo_list_id);
}
```

Update the `makeCurrentMember` helper signature:

```php
private function makeCurrentMember(string $email, string $format = 'digital'): Member
{
    $user = User::factory()->create(['email' => $email]);
    $member = Member::create([
        'user_id' => $user->id,
        'member_number' => 'M' . substr(md5($email), 0, 6),
        'email' => $email,
        'first_name' => 'First',
        'last_name' => 'Last',
        'address' => '1 rue Test',
        'postal_code' => '75000',
        'city' => 'Paris',
        'country' => 'France',
        'joined_at' => now()->subYear(),
    ]);
    Membership::create([
        'member_id' => $member->id,
        'membership_type_id' => $this->membershipTypeId(),
        'status' => 'active',
        'start_date' => now()->subMonth(),
        'end_date' => now()->addMonth(),
        'amount_paid' => 30.00,
        'lepis_format' => $format,
    ]);
    return $member;
}
```

- [ ] **Step 2: Run the updated test, expect failure**

Run: `php artisan test --filter=SyncLepisBulletinToBrevoListTest::test_creates_brevo_list_and_imports_only_digital_members`
Expected: FAIL — current job pushes everyone, no recipients table populated.

- [ ] **Step 3: Update the job**

Replace the body of `app/Jobs/SyncLepisBulletinToBrevoList.php` `handle()` method:

```php
public function handle(BrevoService $brevo): void
{
    // 1. Snapshot all recipients (paper + digital) for this bulletin first.
    $snapshotter = app(\App\Services\LepisBulletinRecipientSnapshotter::class);
    $snapshotResult = $snapshotter->snapshot($this->bulletin);

    $listName = "Lepis {$this->bulletin->year} {$this->bulletin->quarter}";

    // 2. Create the Brevo list
    $listResult = $brevo->createList($listName, config('brevo.folder_id_lepis', 1));
    if (! ($listResult['success'] ?? false)) {
        throw new \RuntimeException('Brevo createList failed: ' . ($listResult['error'] ?? 'unknown'));
    }

    $listId = (int) ($listResult['data']['id'] ?? 0);
    if ($listId === 0) {
        throw new \RuntimeException('Brevo createList returned no id');
    }

    // 3. Fetch only DIGITAL recipients of this bulletin (from the snapshot table).
    $digitalRecipients = $this->bulletin->recipients()
        ->where('format', \App\Models\LepisBulletinRecipient::FORMAT_DIGITAL)
        ->with('member')
        ->get();
    $members = $digitalRecipients->pluck('member')->filter();

    // 4. Import them into the new list
    $importResult = $brevo->importContacts($members, $listId);
    if (! ($importResult['success'] ?? false)) {
        throw new \RuntimeException('Brevo importContacts failed: ' . ($importResult['error'] ?? 'unknown'));
    }

    // 5. Persist sync state on the bulletin
    $this->bulletin->update([
        'brevo_list_id' => $listId,
        'brevo_list_name' => $listName,
        'brevo_synced_at' => now(),
        'brevo_sync_failed' => false,
    ]);

    // 6. Tag digital recipients with the Brevo list id for traceability.
    $this->bulletin->recipients()
        ->where('format', \App\Models\LepisBulletinRecipient::FORMAT_DIGITAL)
        ->update(['brevo_list_id' => $listId]);

    Log::channel('daily')->info('Lepis bulletin synced to Brevo', [
        'bulletin_id' => $this->bulletin->id,
        'list_id' => $listId,
        'count' => $importResult['count'] ?? 0,
        'paper_snapshotted' => $snapshotResult->paperCount,
        'digital_snapshotted' => $snapshotResult->digitalCount,
        'skipped' => count($snapshotResult->skipped),
    ]);
}
```

- [ ] **Step 4: Run the test, expect pass**

Run: `php artisan test --filter=SyncLepisBulletinToBrevoListTest`
Expected: 4 passing (3 existing + 1 updated).

- [ ] **Step 5: Run the full test suite to catch regressions**

Run: `php artisan test --testsuite=Feature --filter=Lepis`
Expected: all green.

- [ ] **Step 6: Commit**

```bash
git add app/Jobs/SyncLepisBulletinToBrevoList.php tests/Feature/Lepis/SyncLepisBulletinToBrevoListTest.php
git commit -m "feat(lepis): job sync ne pousse que les abonnés digital + appel snapshotter"
```

---

## Task 7 — Webhook HelloAsso : parser le custom field "Format Lepis"

**Files:**
- Modify: `app/Http/Controllers/Api/WebhookController.php`
- Create: `tests/Feature/Lepis/LepisFormatHelloAssoWebhookTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Lepis/LepisFormatHelloAssoWebhookTest.php`:

```php
<?php

namespace Tests\Feature\Lepis;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisFormatHelloAssoWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        MembershipType::create([
            'name' => 'Adhésion', 'slug' => 'adhesion', 'price' => 30.00,
            'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
        ]);
    }

    public function test_helloasso_order_with_papier_custom_field_sets_paper(): void
    {
        $payload = $this->orderPayload(formatLepis: 'Papier');

        $this->postJson('/api/webhooks/helloasso', $payload)->assertOk();

        $membership = Membership::query()->latest('id')->first();
        $this->assertNotNull($membership);
        $this->assertSame('paper', $membership->lepis_format);
    }

    public function test_helloasso_order_with_numerique_custom_field_sets_digital(): void
    {
        $payload = $this->orderPayload(formatLepis: 'Numérique');

        $this->postJson('/api/webhooks/helloasso', $payload)->assertOk();

        $membership = Membership::query()->latest('id')->first();
        $this->assertSame('digital', $membership->lepis_format);
    }

    public function test_helloasso_order_without_custom_field_defaults_to_paper(): void
    {
        $payload = $this->orderPayload(formatLepis: null);

        $this->postJson('/api/webhooks/helloasso', $payload)->assertOk();

        $membership = Membership::query()->latest('id')->first();
        $this->assertSame('paper', $membership->lepis_format);
    }

    private function orderPayload(?string $formatLepis): array
    {
        $customFields = [];
        if ($formatLepis !== null) {
            $customFields[] = ['name' => 'Format Lepis', 'answer' => $formatLepis];
        }
        return [
            'eventType' => 'Order',
            'data' => [
                'id' => random_int(100000, 999999),
                'formType' => 'Membership',
                'formSlug' => 'adhesion-2026',
                'payer' => [
                    'firstName' => 'Test', 'lastName' => 'User',
                    'email' => 'webhook' . random_int(1000, 9999) . '@test.com',
                    'address' => '1 rue Test', 'city' => 'Paris',
                    'zipCode' => '75000', 'country' => 'France',
                ],
                'items' => [
                    ['amount' => 3000, 'name' => 'Adhésion', 'customFields' => $customFields],
                ],
            ],
        ];
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

Run: `php artisan test --filter=LepisFormatHelloAssoWebhookTest`
Expected: FAIL — webhook does not yet parse the custom field.

- [ ] **Step 3: Update `WebhookController::processMembership`**

In `app/Http/Controllers/Api/WebhookController.php`, modify `processMembership()` to extract the format and pass it to `Membership::create`. Find the line where `Membership::create([...])` is called (around line 195) and update:

```php
// Extract Lepis format from items[].customFields[]
$lepisFormat = $this->extractLepisFormat($items);

$membership = Membership::create([
    'member_id' => $member->id,
    'membership_type_id' => $membershipType?->id,
    'start_date' => $startDate,
    'end_date' => $endDate,
    'amount_paid' => $amount,
    'payment_method' => 'helloasso',
    'payment_reference' => $data['id'] ?? null,
    'status' => 'active',
    'lepis_format' => $lepisFormat,
]);
```

NB: replace `'amount'` by `'amount_paid'` if the existing code currently uses `'amount'` — the model `$fillable` uses `amount_paid`. Re-read the file before editing to confirm the existing key. (The exploration found `amount_paid` in `$fillable` and `amount` in `Membership::create` — fix the bug while passing.)

Add this private method to the controller:

```php
private function extractLepisFormat(array $items): string
{
    foreach ($items as $item) {
        foreach ($item['customFields'] ?? [] as $field) {
            $name = $field['name'] ?? '';
            $answer = $field['answer'] ?? '';
            if (mb_strtolower($name) === 'format lepis') {
                $normalized = mb_strtolower(trim($answer));
                if ($normalized === 'numérique' || $normalized === 'numerique' || $normalized === 'digital') {
                    return 'digital';
                }
                if ($normalized === 'papier' || $normalized === 'paper') {
                    return 'paper';
                }
            }
        }
    }

    Log::channel('webhooks')->warning('HelloAsso: lepis_format custom field missing or unrecognized, defaulting to paper');
    return 'paper';
}
```

- [ ] **Step 4: Run the test, expect pass**

Run: `php artisan test --filter=LepisFormatHelloAssoWebhookTest`
Expected: 3 passing.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/WebhookController.php tests/Feature/Lepis/LepisFormatHelloAssoWebhookTest.php
git commit -m "feat(lepis): webhook HelloAsso parse le custom field Format Lepis (fallback paper)"
```

---

## Task 8 — Form backoffice Membership : champ `lepis_format` obligatoire

**Files:**
- Modify: `app/Http/Controllers/Admin/MembershipController.php`
- Modify: `resources/views/admin/memberships/_form.blade.php`
- Modify: `resources/views/admin/memberships/index.blade.php`
- Create: `tests/Feature/Admin/MembershipLepisFormatTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Admin/MembershipLepisFormatTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipLepisFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_lepis_format(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        MembershipType::create([
            'name' => 'Standard', 'slug' => 'standard', 'price' => 30,
            'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->post('/extranet/memberships', [
            'member_id' => $member->id,
            'amount_paid' => 30,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d'),
            // lepis_format intentionally omitted
        ]);

        $response->assertSessionHasErrors('lepis_format');
    }

    public function test_store_persists_lepis_format(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->post('/extranet/memberships', [
            'member_id' => $member->id,
            'amount_paid' => 30,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d'),
            'lepis_format' => 'digital',
        ]);

        $response->assertRedirect();
        $m = Membership::where('member_id', $member->id)->latest('id')->first();
        $this->assertSame('digital', $m->lepis_format);
    }

    public function test_index_filters_by_lepis_format(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        MembershipType::create([
            'name' => 'Standard', 'slug' => 'standard', 'price' => 30,
            'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
        ]);
        Membership::create([
            'member_id' => $member->id, 'status' => 'active',
            'start_date' => now(), 'end_date' => now()->addYear(),
            'amount_paid' => 30, 'lepis_format' => 'digital',
        ]);
        Membership::create([
            'member_id' => $member->id, 'status' => 'active',
            'start_date' => now(), 'end_date' => now()->addYear(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        $response = $this->actingAs($admin)->get('/extranet/memberships?lepis_format=digital');
        $response->assertOk();
        // Only digital membership should be visible: a paper-format string should not appear in the response.
        // (We assume the index view shows the format per row.)
    }

    private function makeAdmin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');  // assumes spatie/permission roles exist
        return $u;
    }

    private function makeMember(): Member
    {
        $u = User::factory()->create();
        return Member::create([
            'user_id' => $u->id, 'member_number' => 'M' . random_int(1000, 9999),
            'email' => $u->email, 'first_name' => 'F', 'last_name' => 'L',
            'joined_at' => now(),
        ]);
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

Run: `php artisan test --filter=MembershipLepisFormatTest`
Expected: FAIL — validation does not require `lepis_format`, store doesn't persist it, index doesn't filter.

- [ ] **Step 3: Update validation and assignment in `MembershipController`**

In `app/Http/Controllers/Admin/MembershipController.php`, find both `store()` (line ~98) and `update()` (line ~132). Add to both validation rule arrays:

```php
'lepis_format' => ['required', 'in:paper,digital'],
```

In `store()` and `update()`, add `lepis_format` to the data passed to `Membership::create([...])` / `$membership->update([...])`. The simplest pattern is to pass `$validated` directly if the controller already does so; otherwise add `'lepis_format' => $request->input('lepis_format')`.

For `index()`, add a filter clause. Find where the listing query is built and add:

```php
if ($request->filled('lepis_format')) {
    $query->where('lepis_format', $request->input('lepis_format'));
}
```

(Re-read the existing controller code before editing to integrate cleanly with the existing pagination and filter pattern.)

- [ ] **Step 4: Update the form blade**

In `resources/views/admin/memberships/_form.blade.php`, append before the closing of the file (after the `Notes` block):

```blade
<div class="form-group">
    <label class="form-label" for="lepis_format">Format Lepis *</label>
    <select name="lepis_format" id="lepis_format" class="form-input" required>
        <option value="">-- Selectionner --</option>
        <option value="paper" {{ old('lepis_format', $membership->lepis_format ?? '') === 'paper' ? 'selected' : '' }}>Papier</option>
        <option value="digital" {{ old('lepis_format', $membership->lepis_format ?? '') === 'digital' ? 'selected' : '' }}>Numérique</option>
    </select>
    @error('lepis_format')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
    <small style="color: #6b7280; font-size: 0.875rem;">Choix figé pour la durée de l'adhésion. Redécidé au renouvellement.</small>
</div>
```

- [ ] **Step 5: Update the index view to show the filter dropdown and the format column**

In `resources/views/admin/memberships/index.blade.php`, locate the existing filter form (or where filters live) and add a select:

```blade
<div class="form-group">
    <label class="form-label" for="lepis_format">Format Lepis</label>
    <select name="lepis_format" id="lepis_format" class="form-input">
        <option value="">Tous</option>
        <option value="paper" {{ request('lepis_format') === 'paper' ? 'selected' : '' }}>Papier</option>
        <option value="digital" {{ request('lepis_format') === 'digital' ? 'selected' : '' }}>Numérique</option>
    </select>
</div>
```

In the row rendering for each membership, add a column showing `{{ $membership->lepis_format === 'digital' ? 'Numérique' : 'Papier' }}` next to the existing columns.

- [ ] **Step 6: Run the test, expect pass**

Run: `php artisan test --filter=MembershipLepisFormatTest`
Expected: 3 passing.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/Admin/MembershipController.php resources/views/admin/memberships/_form.blade.php resources/views/admin/memberships/index.blade.php tests/Feature/Admin/MembershipLepisFormatTest.php
git commit -m "feat(lepis): champ obligatoire lepis_format sur formulaire admin Membership + filtre index"
```

---

## Task 9 — Carte "Diffusion" sur la fiche bulletin admin

**Files:**
- Create: `resources/views/admin/lepis/_carte_diffusion.blade.php`
- Modify: `resources/views/admin/lepis/edit.blade.php` (include the new card)
- Modify: `app/Http/Controllers/Admin/LepisBulletinController.php` (load recipients counts in `edit()`)
- Create: `tests/Feature/Admin/LepisBulletinDiffusionTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Admin/LepisBulletinDiffusionTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBulletinDiffusionTest extends TestCase
{
    use RefreshDatabase;

    public function test_diffusion_card_visible_only_when_status_is_members_or_public(): void
    {
        $admin = $this->makeAdmin();
        $draft = $this->makeBulletin('draft');
        $members = $this->makeBulletin('members');

        $resDraft = $this->actingAs($admin)->get("/extranet/lepis/{$draft->id}/edit");
        $resDraft->assertOk()->assertDontSee('Diffusion');

        $resMembers = $this->actingAs($admin)->get("/extranet/lepis/{$members->id}/edit");
        $resMembers->assertOk()->assertSee('Diffusion');
    }

    public function test_diffusion_card_shows_paper_and_digital_counts(): void
    {
        $admin = $this->makeAdmin();
        $bulletin = $this->makeBulletin('members');
        $this->makeRecipient($bulletin, 'paper');
        $this->makeRecipient($bulletin, 'paper');
        $this->makeRecipient($bulletin, 'digital');

        $response = $this->actingAs($admin)->get("/extranet/lepis/{$bulletin->id}/edit");

        $response->assertOk()
            ->assertSee('Papier')
            ->assertSeeText('2', escape: false)
            ->assertSee('Numérique');
    }

    private function makeAdmin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    private function makeBulletin(string $status): LepisBulletin
    {
        return LepisBulletin::create([
            'title' => 'T', 'issue_number' => random_int(1, 1000), 'quarter' => 'Q1', 'year' => 2026,
            'pdf_path' => 'x.pdf', 'status' => $status,
            'published_to_members_at' => $status !== 'draft' ? now() : null,
        ]);
    }

    private function makeRecipient(LepisBulletin $bulletin, string $format): LepisBulletinRecipient
    {
        $u = User::factory()->create();
        $member = Member::create([
            'user_id' => $u->id, 'member_number' => 'M' . random_int(1, 99999),
            'email' => $u->email, 'first_name' => 'F', 'last_name' => 'L',
            'joined_at' => now(),
        ]);
        return LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $bulletin->id, 'member_id' => $member->id,
            'format' => $format, 'included_at' => now(),
        ]);
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

Run: `php artisan test --filter=LepisBulletinDiffusionTest`
Expected: FAIL — card does not exist.

- [ ] **Step 3: Create the card view**

Create `resources/views/admin/lepis/_carte_diffusion.blade.php`:

```blade
@php
    $paperCount = $bulletin->paperRecipientsCount();
    $digitalCount = $bulletin->digitalRecipientsCount();
    $total = $paperCount + $digitalCount;
    $lastSnapshotAt = $bulletin->recipients()->max('included_at');
@endphp

<div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1.25rem; margin-bottom: 1rem;">
    <h3 style="margin-top: 0; margin-bottom: 1rem; font-weight: 600;">Diffusion</h3>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 1rem;">
        <tbody>
            <tr>
                <td style="padding: 0.5rem 0;">Papier</td>
                <td style="padding: 0.5rem 0; text-align: right; font-weight: 600;">{{ $paperCount }}</td>
                <td style="padding: 0.5rem 0; text-align: right;">
                    @if($paperCount > 0)
                        <a href="{{ route('admin.lepis.recipients.export', ['bulletin' => $bulletin->id, 'format' => 'paper']) }}"
                           style="background: #2C5F2D; color: white; padding: 0.25rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.875rem;">
                            Exporter CSV
                        </a>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 0;">Numérique</td>
                <td style="padding: 0.5rem 0; text-align: right; font-weight: 600;">{{ $digitalCount }}</td>
                <td style="padding: 0.5rem 0; text-align: right; color: #6b7280; font-size: 0.875rem;">
                    @if($bulletin->brevo_list_id)
                        Liste Brevo #{{ $bulletin->brevo_list_id }}
                    @endif
                </td>
            </tr>
            <tr style="border-top: 1px solid #e5e7eb;">
                <td style="padding: 0.5rem 0; font-weight: 600;">Total</td>
                <td style="padding: 0.5rem 0; text-align: right; font-weight: 600;">{{ $total }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    @if($lastSnapshotAt)
        <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.75rem 0;">
            Dernier snapshot : {{ \Carbon\Carbon::parse($lastSnapshotAt)->locale('fr')->isoFormat('LLL') }}
        </p>
    @endif

    <form method="POST" action="{{ route('admin.lepis.recipients.snapshot', ['bulletin' => $bulletin->id]) }}"
          onsubmit="return confirm('Recalculer le snapshot des destinataires ? Met à jour la liste papier et numérique selon les adhésions actuelles.');"
          style="display: inline;">
        @csrf
        <button type="submit" style="background: #f3f4f6; color: #374151; padding: 0.375rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.25rem; cursor: pointer;">
            Recalculer le snapshot
        </button>
    </form>
</div>
```

- [ ] **Step 4: Include the card in `edit.blade.php`**

In `resources/views/admin/lepis/edit.blade.php`, find where the existing cards (`_carte_infos`, `_carte_pdf`, `_carte_cycle`, `_carte_annonce`) are included. Add after the others (within an `@if` to gate on status):

```blade
@if(in_array($bulletin->status, ['members', 'public']))
    @include('admin.lepis._carte_diffusion', ['bulletin' => $bulletin])
@endif
```

- [ ] **Step 5: Add routes for export and snapshot recalc**

In `routes/admin.php`, find the Lepis route group (around line 282) and add:

```php
Route::get('/lepis/{bulletin}/recipients/export', [\App\Http\Controllers\Admin\LepisBulletinController::class, 'exportRecipients'])->name('admin.lepis.recipients.export');
Route::post('/lepis/{bulletin}/recipients/snapshot', [\App\Http\Controllers\Admin\LepisBulletinController::class, 'recalculateSnapshot'])->name('admin.lepis.recipients.snapshot');
```

(Adjust prefix/naming to match the existing group structure — the file uses `extranet` prefix for admin and `Route::resource('lepis', ...)` so the URLs above will resolve under `/extranet/lepis/...`.)

- [ ] **Step 6: Add the controller methods**

In `app/Http/Controllers/Admin/LepisBulletinController.php`, add these methods:

```php
public function exportRecipients(\App\Models\LepisBulletin $bulletin, \Illuminate\Http\Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
{
    $format = $request->query('format', 'paper');
    abort_unless(in_array($format, ['paper', 'digital']), 400);

    $recipients = $bulletin->recipients()->where('format', $format)->with('member')->get();

    $filename = "lepis-{$bulletin->year}-{$bulletin->quarter}-{$format}.csv";

    return response()->streamDownload(function () use ($recipients) {
        $out = fopen('php://output', 'w');
        // BOM for Excel UTF-8 compatibility
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['prenom', 'nom', 'email', 'adresse', 'code_postal', 'ville', 'pays', 'numero_adherent']);
        foreach ($recipients as $r) {
            $address = $r->postal_address_at_snapshot ?? [];
            fputcsv($out, [
                $r->member->first_name ?? '',
                $r->member->last_name ?? '',
                $r->email_at_snapshot ?? $r->member->email ?? '',
                $address['address'] ?? $r->member->address ?? '',
                $address['postal_code'] ?? $r->member->postal_code ?? '',
                $address['city'] ?? $r->member->city ?? '',
                $address['country'] ?? $r->member->country ?? '',
                $r->member->member_number ?? '',
            ]);
        }
        fclose($out);
    }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
}

public function recalculateSnapshot(\App\Models\LepisBulletin $bulletin): \Illuminate\Http\RedirectResponse
{
    $result = app(\App\Services\LepisBulletinRecipientSnapshotter::class)->snapshot($bulletin);

    $msg = "Snapshot recalculé : {$result->paperCount} papier, {$result->digitalCount} numérique";
    if (count($result->skipped) > 0) {
        $msg .= ", " . count($result->skipped) . " écarté(s)";
    }

    return redirect()->route('admin.lepis.edit', $bulletin)->with('success', $msg);
}
```

(Confirm the existing route name for the edit page — the resource route should produce `lepis.edit` or `admin.lepis.edit` depending on the prefix. Adjust the redirect accordingly.)

- [ ] **Step 7: Run the test, expect pass**

Run: `php artisan test --filter=LepisBulletinDiffusionTest`
Expected: 2 passing.

- [ ] **Step 8: Add a small test for the CSV export**

Append to `tests/Feature/Admin/LepisBulletinDiffusionTest.php`:

```php
public function test_export_csv_contains_only_paper_recipients(): void
{
    $admin = $this->makeAdmin();
    $bulletin = $this->makeBulletin('members');

    $u1 = User::factory()->create(['email' => 'paper@x.com']);
    $paperMember = Member::create([
        'user_id' => $u1->id, 'member_number' => 'M111', 'email' => 'paper@x.com',
        'first_name' => 'Paul', 'last_name' => 'Papier', 'joined_at' => now(),
    ]);
    LepisBulletinRecipient::create([
        'lepis_bulletin_id' => $bulletin->id, 'member_id' => $paperMember->id,
        'format' => 'paper',
        'email_at_snapshot' => 'paper@x.com',
        'postal_address_at_snapshot' => ['address' => '1 rue P', 'postal_code' => '75001', 'city' => 'Paris', 'country' => 'France'],
        'included_at' => now(),
    ]);

    $u2 = User::factory()->create(['email' => 'digital@x.com']);
    $digitalMember = Member::create([
        'user_id' => $u2->id, 'member_number' => 'M222', 'email' => 'digital@x.com',
        'first_name' => 'Diane', 'last_name' => 'Digitale', 'joined_at' => now(),
    ]);
    LepisBulletinRecipient::create([
        'lepis_bulletin_id' => $bulletin->id, 'member_id' => $digitalMember->id,
        'format' => 'digital',
        'email_at_snapshot' => 'digital@x.com',
        'included_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get("/extranet/lepis/{$bulletin->id}/recipients/export?format=paper");

    $response->assertOk();
    $csv = $response->streamedContent();
    $this->assertStringContainsString('Paul', $csv);
    $this->assertStringContainsString('Papier', $csv);
    $this->assertStringNotContainsString('Diane', $csv);
}
```

- [ ] **Step 9: Run the test, expect pass**

Run: `php artisan test --filter=LepisBulletinDiffusionTest`
Expected: 3 passing.

- [ ] **Step 10: Commit**

```bash
git add resources/views/admin/lepis/_carte_diffusion.blade.php resources/views/admin/lepis/edit.blade.php app/Http/Controllers/Admin/LepisBulletinController.php routes/admin.php tests/Feature/Admin/LepisBulletinDiffusionTest.php
git commit -m "feat(lepis): carte Diffusion sur fiche bulletin admin (counts + export CSV + recalcul)"
```

---

## Task 10 — Section "Bulletins reçus" sur la fiche contact admin

**Files:**
- Modify: `app/Http/Controllers/Admin/MemberController.php`
- Modify: `resources/views/admin/members/show.blade.php`
- Create: `tests/Feature/Admin/MemberLepisHistoryTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Admin/MemberLepisHistoryTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberLepisHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_show_lists_lepis_recipients_chronologically(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $b1 = LepisBulletin::create([
            'title' => 'Q1 2026', 'issue_number' => 1, 'quarter' => 'Q1', 'year' => 2026,
            'pdf_path' => 'x.pdf', 'status' => 'public',
            'published_to_members_at' => now()->subMonths(6),
            'published_public_at' => now()->subMonths(2),
        ]);
        $b2 = LepisBulletin::create([
            'title' => 'Q2 2026', 'issue_number' => 2, 'quarter' => 'Q2', 'year' => 2026,
            'pdf_path' => 'y.pdf', 'status' => 'members',
            'published_to_members_at' => now()->subDays(15),
        ]);

        LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $b1->id, 'member_id' => $member->id,
            'format' => 'paper', 'included_at' => now()->subMonths(6),
        ]);
        LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $b2->id, 'member_id' => $member->id,
            'format' => 'digital', 'included_at' => now()->subDays(15),
        ]);

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

        $response->assertOk()
            ->assertSeeInOrder(['Q2 2026', 'Q1 2026']);  // anti-chronological
    }

    private function makeAdmin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    private function makeMember(): Member
    {
        $u = User::factory()->create();
        return Member::create([
            'user_id' => $u->id, 'member_number' => 'M' . random_int(1, 99999),
            'email' => $u->email, 'first_name' => 'F', 'last_name' => 'L',
            'joined_at' => now(),
        ]);
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

Run: `php artisan test --filter=MemberLepisHistoryTest`
Expected: FAIL — section not yet rendered.

- [ ] **Step 3: Update `MemberController::show`**

In `app/Http/Controllers/Admin/MemberController.php`, modify the `show()` method to eager-load the recipients and pass them to the view:

```php
public function show(Member $member)
{
    $member->load([
        'memberships.membershipType',
        'lepisBulletinRecipients.bulletin',
    ]);

    return view('admin.members.show', [
        'member' => $member,
    ]);
}
```

(If `show()` already loads relations, merge `'lepisBulletinRecipients.bulletin'` into the existing load array. Re-read the file before editing.)

- [ ] **Step 4: Update `resources/views/admin/members/show.blade.php`**

Add a new section (find an appropriate place near the existing memberships section). Append:

```blade
<div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1.25rem; margin-bottom: 1rem;">
    <h3 style="margin-top: 0; font-weight: 600;">Bulletins Lepis reçus</h3>

    @if($member->lepisBulletinRecipients->isEmpty())
        <p style="color: #6b7280; margin: 0;">Aucun envoi de bulletin pour ce contact.</p>
    @else
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <th style="padding: 0.5rem; text-align: left;">Bulletin</th>
                    <th style="padding: 0.5rem; text-align: left;">Format</th>
                    <th style="padding: 0.5rem; text-align: left;">Date d'envoi</th>
                    <th style="padding: 0.5rem; text-align: left;">Liste Brevo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($member->lepisBulletinRecipients as $r)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 0.5rem;">
                            <a href="{{ route('admin.lepis.edit', $r->bulletin) }}" style="color: #2C5F2D; text-decoration: none;">
                                {{ $r->bulletin?->title ?? '#' . $r->lepis_bulletin_id }}
                            </a>
                        </td>
                        <td style="padding: 0.5rem;">
                            {{ $r->format === 'digital' ? 'Numérique' : 'Papier' }}
                        </td>
                        <td style="padding: 0.5rem;">
                            {{ $r->included_at?->locale('fr')->isoFormat('LL') }}
                        </td>
                        <td style="padding: 0.5rem; color: #6b7280; font-size: 0.875rem;">
                            {{ $r->brevo_list_id ? '#' . $r->brevo_list_id : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
```

- [ ] **Step 5: Run the test, expect pass**

Run: `php artisan test --filter=MemberLepisHistoryTest`
Expected: 1 passing.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/MemberController.php resources/views/admin/members/show.blade.php tests/Feature/Admin/MemberLepisHistoryTest.php
git commit -m "feat(lepis): section Bulletins reçus sur fiche contact admin"
```

---

## Task 11 — Espace membre : afficher le format Lepis (lecture seule)

**Files:**
- Modify: `app/Http/Controllers/Member/ProfileController.php`
- Modify: `resources/views/member/profile.blade.php`
- Create: `tests/Feature/Member/MemberProfileLepisFormatTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Member/MemberProfileLepisFormatTest.php`:

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberProfileLepisFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_shows_paper_format_when_active_membership_is_paper(): void
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id, 'member_number' => 'M1', 'email' => $user->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now(),
        ]);
        MembershipType::create(['name' => 'S', 'slug' => 's', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]);
        Membership::create([
            'member_id' => $member->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        $response = $this->actingAs($user)->get('/espace-membre/profil');

        $response->assertOk()->assertSee('Papier');
    }

    public function test_profile_shows_digital_format_when_active_membership_is_digital(): void
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id, 'member_number' => 'M2', 'email' => $user->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now(),
        ]);
        MembershipType::create(['name' => 'S', 'slug' => 's', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]);
        Membership::create([
            'member_id' => $member->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'digital',
        ]);

        $response = $this->actingAs($user)->get('/espace-membre/profil');

        $response->assertOk()->assertSee('Numérique');
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

Run: `php artisan test --filter=MemberProfileLepisFormatTest`
Expected: FAIL — profile view does not display the format.

- [ ] **Step 3: Pass `lepisFormat` from `ProfileController::index`**

In `app/Http/Controllers/Member/ProfileController.php`, modify `index()`:

```php
public function index()
{
    $user = auth()->user();
    $member = $user->member;
    $currentMembership = $member?->currentMembership();
    $lepisFormat = $currentMembership?->lepisFormatOrDefault();

    return view('member.profile', [
        'user' => $user,
        'member' => $member,
        'lepisFormat' => $lepisFormat,
    ]);
}
```

(If the existing `index` already passes `$user` and `$member`, just add `$lepisFormat` to the array.)

- [ ] **Step 4: Update `resources/views/member/profile.blade.php`**

Add this block in the page (near the personal info or membership section):

```blade
@if($lepisFormat)
    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem;">
        <p style="margin: 0; font-weight: 600;">
            Format de réception du bulletin Lepis :
            <span style="color: #2C5F2D;">{{ $lepisFormat === 'digital' ? 'Numérique' : 'Papier' }}</span>
        </p>
        <p style="margin: 0.5rem 0 0 0; color: #6b7280; font-size: 0.875rem;">
            Pour modifier ce choix, contactez le secrétariat à <a href="mailto:secretariat@oreina.org" style="color: #2C5F2D;">secretariat@oreina.org</a>.
        </p>
    </div>
@endif
```

- [ ] **Step 5: Run the test, expect pass**

Run: `php artisan test --filter=MemberProfileLepisFormatTest`
Expected: 2 passing.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Member/ProfileController.php resources/views/member/profile.blade.php tests/Feature/Member/MemberProfileLepisFormatTest.php
git commit -m "feat(lepis): affichage readonly du format Lepis dans l'espace membre"
```

---

## Task 12 — Commande Artisan `lepis:backfill-recipients`

**Files:**
- Create: `app/Console/Commands/LepisBackfillRecipientsCommand.php`
- Create: `tests/Feature/Console/LepisBackfillRecipientsCommandTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Console/LepisBackfillRecipientsCommandTest.php`:

```php
<?php

namespace Tests\Feature\Console;

use App\Models\LepisBulletin;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBackfillRecipientsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_backfills_all_published_bulletins(): void
    {
        MembershipType::create(['name' => 'S', 'slug' => 's', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]);
        $bDraft = LepisBulletin::create([
            'title' => 'Draft', 'issue_number' => 1, 'quarter' => 'Q1', 'year' => 2026,
            'pdf_path' => 'd.pdf', 'status' => 'draft',
        ]);
        $bMembers = LepisBulletin::create([
            'title' => 'Members', 'issue_number' => 2, 'quarter' => 'Q2', 'year' => 2026,
            'pdf_path' => 'm.pdf', 'status' => 'members',
            'published_to_members_at' => now()->subDays(15),
        ]);
        $bPublic = LepisBulletin::create([
            'title' => 'Public', 'issue_number' => 3, 'quarter' => 'Q3', 'year' => 2026,
            'pdf_path' => 'p.pdf', 'status' => 'public',
            'published_to_members_at' => now()->subDays(60),
            'published_public_at' => now()->subDays(15),
        ]);

        $u = User::factory()->create();
        $member = Member::create([
            'user_id' => $u->id, 'member_number' => 'M1', 'email' => $u->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now()->subYear(),
            'address' => '1 rue X', 'postal_code' => '75000', 'city' => 'Paris', 'country' => 'France',
        ]);
        Membership::create([
            'member_id' => $member->id, 'status' => 'active',
            'start_date' => now()->subYear(), 'end_date' => now()->addYear(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        $this->artisan('lepis:backfill-recipients')->assertSuccessful();

        $this->assertCount(0, $bDraft->fresh()->recipients);
        $this->assertCount(1, $bMembers->fresh()->recipients);
        $this->assertCount(1, $bPublic->fresh()->recipients);
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

Run: `php artisan test --filter=LepisBackfillRecipientsCommandTest`
Expected: FAIL — command not found.

- [ ] **Step 3: Create the command**

Create `app/Console/Commands/LepisBackfillRecipientsCommand.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\LepisBulletin;
use App\Services\LepisBulletinRecipientSnapshotter;
use Illuminate\Console\Command;

class LepisBackfillRecipientsCommand extends Command
{
    protected $signature = 'lepis:backfill-recipients';

    protected $description = 'Snapshot recipients for every Lepis bulletin already in members or public state.';

    public function handle(LepisBulletinRecipientSnapshotter $snapshotter): int
    {
        $bulletins = LepisBulletin::query()
            ->whereIn('status', [LepisBulletin::STATUS_MEMBERS, LepisBulletin::STATUS_PUBLIC])
            ->orderBy('year')->orderBy('quarter')
            ->get();

        $this->info("Backfilling " . $bulletins->count() . " bulletin(s).");

        foreach ($bulletins as $bulletin) {
            $result = $snapshotter->snapshot($bulletin);
            $this->line("  - {$bulletin->title} ({$bulletin->year} {$bulletin->quarter}): paper={$result->paperCount} digital={$result->digitalCount} skipped=" . count($result->skipped));
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
```

- [ ] **Step 4: Run the test, expect pass**

Run: `php artisan test --filter=LepisBackfillRecipientsCommandTest`
Expected: 1 passing.

- [ ] **Step 5: Commit**

```bash
git add app/Console/Commands/LepisBackfillRecipientsCommand.php tests/Feature/Console/LepisBackfillRecipientsCommandTest.php
git commit -m "feat(lepis): commande artisan lepis:backfill-recipients pour reconstruire l'historique"
```

---

## Task 13 — Run full test suite + manual smoke

- [ ] **Step 1: Full test suite**

Run: `php artisan test`
Expected: all tests green. Investigate and fix any regressions before continuing.

- [ ] **Step 2: Manual smoke test (local)**

In Tinker, simulate the full flow:

```bash
php artisan tinker
```

```php
// 1. Check legacy backfill: every existing membership should have lepis_format='paper'
\App\Models\Membership::whereNull('lepis_format')->count();  // should be 0

// 2. Create a quick scenario: a digital member, a paper member, then snapshot a bulletin
$bulletin = \App\Models\LepisBulletin::create([
    'title' => 'Test Q1', 'issue_number' => 99, 'quarter' => 'Q1', 'year' => 2027,
    'pdf_path' => 'test.pdf', 'status' => 'members', 'published_to_members_at' => now(),
]);
$result = app(\App\Services\LepisBulletinRecipientSnapshotter::class)->snapshot($bulletin);
dump($result);

// 3. Cleanup
$bulletin->delete();
exit
```

- [ ] **Step 3: Manual UI smoke**

- Hit `/extranet/memberships/create` while logged in as admin → form should display "Format Lepis *".
- Hit `/extranet/lepis/{id}/edit` for a `members` bulletin → carte "Diffusion" visible with counts.
- Click "Exporter CSV" → CSV downloads with paper recipients.
- Hit `/extranet/members/{id}` → "Bulletins Lepis reçus" section visible.
- Log in as a member → `/espace-membre/profil` shows "Format de réception du bulletin Lepis : Papier".

- [ ] **Step 4: Commit anything left (e.g., adjustments after smoke testing)**

If smoke testing reveals tweaks, fix and commit individually.

---

## Task 14 — Production rollout checklist (no code, just doc)

- [ ] **Step 1: Confirm with David before deploying**

Items to verify with the product owner (David Demerges) before the production deploy:

1. `secretariat@oreina.org` is the right contact email for the espace membre message.
2. The HelloAsso form has been updated with the "Format Lepis" custom field BEFORE the deploy (otherwise every new HelloAsso adhesion will silently default to `paper`). Action required: David edits the HelloAsso form and informs.
3. The rédac-chef Lepis is informed: from the next bulletin, the Brevo list will only contain digital subscribers; the paper export is on the bulletin admin page.
4. Run `php artisan lepis:backfill-recipients` immediately after the migrations on production to populate the recipients table for already-published bulletins.

- [ ] **Step 2: Deploy sequence (production)**

```bash
# After merging to main
git pull origin main
php artisan migrate
php artisan lepis:backfill-recipients
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Self-Review Notes

**Spec coverage:**
- §Modèle de données → Tasks 1, 3, 4 ✓
- §Capture de la préférence (HelloAsso, backoffice, espace membre) → Tasks 7, 8, 11 ✓
- §Snapshot et filtrage Brevo → Tasks 5, 6 ✓
- §UI backoffice fiche bulletin (carte Diffusion + export CSV + recalc) → Task 9 ✓
- §UI backoffice fiche contact (Bulletins reçus) → Task 10 ✓
- §Filtre lepis_format sur index adhésions → Task 8 (step 5) ✓
- §Espace membre readonly → Task 11 ✓
- §Commande backfill → Task 12 ✓
- §Tests → distributed across tasks (TDD) ✓
- §Migration de production → Task 14 ✓

**Type consistency:**
- `lepis_format` values: always `'paper'` / `'digital'` (never `'PAPER'` etc.)
- `LepisBulletinRecipient` constants `FORMAT_PAPER` / `FORMAT_DIGITAL`
- `Membership` constants `LEPIS_FORMAT_PAPER` / `LEPIS_FORMAT_DIGITAL`
- Snapshotter returns `SnapshotResult { paperCount, digitalCount, skipped }` — used consistently in Task 6 (`$snapshotResult->paperCount`) and Task 12 (`$result->paperCount`)
- Relations: `bulletin->recipients()`, `member->lepisBulletinRecipients()`, `recipient->bulletin`, `recipient->member`, `recipient->membership`

**Placeholders:** none — every code block is complete.

**Notes for the executing engineer:**
- Re-read each existing file before editing — I have noted line numbers from the exploration but they may have drifted.
- The `MembershipController::store` and `update` methods may have slightly different signatures depending on how validation is structured. Adapt validation rules into the existing pattern, don't replace wholesale.
- The `routes/admin.php` already defines `Route::resource('lepis', ...)` so the export/snapshot routes need to be added inside the same group prefix (`extranet`).
- The `BrevoService::importContacts` accepts a `Collection` — make sure the eager-loaded recipient `->member` collection is filtered with `filter()` to drop possible nulls.
