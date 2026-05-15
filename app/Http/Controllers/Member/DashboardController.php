<?php

namespace App\Http\Controllers\Member;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\JournalIssue;
use App\Models\LepidopteraOfMonth;
use App\Models\LepisBulletin;
use App\Models\Member;
use App\Models\Submission;
use App\Models\WorkGroup;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        $currentMembership = $member?->currentMembership();
        $isCurrentMember = $member?->isCurrentMember() ?? false;

        $recentDonations = $member?->donations()
            ->orderBy('donation_date', 'desc')
            ->limit(3)
            ->get() ?? collect();

        $latestIssues = collect();
        if ($isCurrentMember) {
            $latestIssues = JournalIssue::where('status', 'published')
                ->orderBy('publication_date', 'desc')
                ->limit(3)
                ->get();
        }

        $workGroups = WorkGroup::active()->withCount('members')->orderBy('name')->limit(3)->get();
        $myGroupIds = $member?->workGroups()->pluck('work_groups.id')->toArray() ?? [];

        // Mes groupes & projets (cards) — groupes dont l'adhérent est membre
        $myWorkGroups = $member?->workGroups()->active()
            ->withCount(['forumThreads', 'resources'])
            ->limit(8)->get() ?? collect();

        $upcomingEvents = Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        $mySubmissions = Submission::where('author_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $articlesPublished = Submission::where('author_id', $user->id)
            ->where('status', SubmissionStatus::Published->value)
            ->count();

        $articlesSubmitted = Submission::where('author_id', $user->id)->count();

        $stats = [
            'total_donations' => $member?->donations()->sum('amount') ?? 0,
            'donation_count' => $member?->donations()->count() ?? 0,
            'membership_years' => $member?->memberships()->where('status', 'active')->count() ?? 0,
            'observations_transmitted' => 0,
            'validations_done' => 0,
            'articles_submitted' => $articlesSubmitted,
            'articles_published' => $articlesPublished,
            'documents_shared' => 0,
        ];

        // Carousel "Espèce du mois"
        $lepidopteraSlides = LepidopteraOfMonth::active()->ordered()->get();

        // Carte "Réseau des adhérents" : clusters par région
        $membersByRegion = $this->computeMembersByRegion();
        $totalActiveMembers = Member::where('is_active', true)->count();
        $randomMemberAvatars = Member::where('is_active', true)
            ->whereNotNull('photo_path')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Dernier bulletin Lepis (pour "Ressources récentes")
        // Statuts réels : draft / members / public. On prend le plus récent visible
        // (members ou public) en s'appuyant sur la date de mise à dispo aux adhérents.
        $latestLepisBulletin = LepisBulletin::visibleOnHub()
            ->orderByDesc('published_to_members_at')
            ->orderByDesc('id')
            ->first();

        // Suggestions
        $suggestionWorkGroup = WorkGroup::active()
            ->whereNotIn('id', $myGroupIds)
            ->inRandomOrder()
            ->first();

        $suggestionArticle = Submission::where('status', SubmissionStatus::Published->value)
            ->latest('updated_at')
            ->first();

        $suggestionEvent = $upcomingEvents->first();

        return view('member.dashboard', compact(
            'user',
            'member',
            'currentMembership',
            'isCurrentMember',
            'recentDonations',
            'latestIssues',
            'workGroups',
            'myWorkGroups',
            'myGroupIds',
            'stats',
            'upcomingEvents',
            'mySubmissions',
            'lepidopteraSlides',
            'membersByRegion',
            'totalActiveMembers',
            'randomMemberAvatars',
            'latestLepisBulletin',
            'suggestionWorkGroup',
            'suggestionArticle',
            'suggestionEvent'
        ));
    }

    /**
     * Agrège les adhérents actifs par région métropolitaine
     * pour les clusters de la carte "Réseau des adhérents".
     * Retourne un tableau ['HDF' => ['label' => ..., 'x' => ..., 'y' => ..., 'count' => N], ...].
     */
    private function computeMembersByRegion(): array
    {
        $rows = Member::where('is_active', true)
            ->whereNotNull('postal_code')
            ->selectRaw('SUBSTRING(postal_code FROM 1 FOR 2) as dept, COUNT(*) as n')
            ->groupBy('dept')
            ->get();

        $deptToRegion = config('regions_members.departments', []);
        $regions = config('regions_members.regions', []);

        $byRegion = [];
        foreach ($regions as $code => $meta) {
            $byRegion[$code] = $meta + ['count' => 0];
        }

        foreach ($rows as $row) {
            $dept = $row->dept;
            $code = $deptToRegion[$dept] ?? null;
            if ($code && isset($byRegion[$code])) {
                $byRegion[$code]['count'] += $row->n;
            }
        }

        return $byRegion;
    }
}
