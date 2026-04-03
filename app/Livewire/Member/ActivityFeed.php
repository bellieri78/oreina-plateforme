<?php

namespace App\Livewire\Member;

use App\Models\Donation;
use App\Models\Event;
use App\Models\JournalIssue;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Support\Collection;
use Livewire\Component;

class ActivityFeed extends Component
{
    public int $memberId;
    public bool $isCurrentMember;

    public function mount(int $memberId, bool $isCurrentMember = false): void
    {
        $this->memberId = $memberId;
        $this->isCurrentMember = $isCurrentMember;
    }

    public function getFeedItems(): Collection
    {
        $items = collect();

        // Recent donations by this member
        $donations = Donation::where('member_id', $this->memberId)
            ->orderBy('donation_date', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($d) => [
                'type' => 'donation',
                'icon' => 'heart',
                'color' => 'amber',
                'title' => 'Don enregistré',
                'description' => number_format($d->amount, 2, ',', ' ') . ' €',
                'date' => $d->donation_date,
            ]);
        $items = $items->merge($donations);

        // Membership changes
        $memberships = Membership::where('member_id', $this->memberId)
            ->orderBy('start_date', 'desc')
            ->limit(3)
            ->get()
            ->map(fn ($m) => [
                'type' => 'membership',
                'icon' => 'id-card',
                'color' => 'green',
                'title' => $m->status === 'active' ? 'Adhésion renouvelée' : 'Adhésion enregistrée',
                'description' => 'Valide jusqu\'au ' . $m->end_date->format('d/m/Y'),
                'date' => $m->start_date,
            ]);
        $items = $items->merge($memberships);

        // Recently published journal issues (global)
        if ($this->isCurrentMember) {
            $issues = JournalIssue::where('status', 'published')
                ->orderBy('publication_date', 'desc')
                ->limit(3)
                ->get()
                ->map(fn ($i) => [
                    'type' => 'journal',
                    'icon' => 'book',
                    'color' => 'blue',
                    'title' => 'Nouveau numéro de la revue',
                    'description' => ($i->title ?? 'OREINA') . ' — Vol. ' . $i->volume_number . ', N°' . $i->issue_number,
                    'date' => $i->publication_date,
                ]);
            $items = $items->merge($issues);
        }

        // Upcoming/recent events (global)
        $events = Event::where('status', 'published')
            ->where('start_date', '>=', now()->subDays(7))
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get()
            ->map(fn ($e) => [
                'type' => 'event',
                'icon' => 'calendar',
                'color' => 'purple',
                'title' => $e->start_date->isFuture() ? 'Événement à venir' : 'Événement récent',
                'description' => $e->title,
                'date' => $e->start_date,
            ]);
        $items = $items->merge($events);

        return $items->sortByDesc('date')->take(10)->values();
    }

    public function render()
    {
        return view('livewire.member.activity-feed', [
            'feedItems' => $this->getFeedItems(),
        ]);
    }
}
