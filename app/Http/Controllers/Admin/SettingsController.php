<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Donation;
use App\Models\Event;
use App\Models\JournalIssue;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::getGrouped();

        $groups = [
            'general' => 'Parametres generaux',
            'journal' => 'Revue scientifique',
            'emails' => 'Emails',
            'memberships' => 'Adhesions',
        ];

        return view('admin.settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Parametres mis a jour avec succes.');
    }

    public function clearCache()
    {
        Setting::clearCache();
        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Cache vide avec succes.');
    }

    public function statistics()
    {
        // General stats
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'by_role' => User::select('role', DB::raw('count(*) as count'))
                    ->groupBy('role')
                    ->pluck('count', 'role')
                    ->toArray(),
            ],
            'members' => [
                'total' => Member::count(),
                'active' => Member::whereHas('memberships', function ($q) {
                    $q->where('status', 'active')
                      ->where('end_date', '>=', now());
                })->count(),
            ],
            'memberships' => [
                'total' => Membership::count(),
                'active' => Membership::where('status', 'active')
                    ->where('end_date', '>=', now())->count(),
                'expired' => Membership::where('end_date', '<', now())->count(),
                'by_type' => Membership::join('membership_types', 'memberships.membership_type_id', '=', 'membership_types.id')
                    ->select('membership_types.name as type_name', DB::raw('count(*) as count'))
                    ->groupBy('membership_types.name')
                    ->pluck('count', 'type_name')
                    ->toArray(),
            ],
            'donations' => [
                'total' => Donation::count(),
                'total_amount' => Donation::sum('amount'),
                'this_year' => Donation::whereYear('donation_date', now()->year)->sum('amount'),
                'by_month' => Donation::select(
                    DB::raw("to_char(donation_date, 'YYYY-MM') as month"),
                    DB::raw('sum(amount) as total')
                )
                    ->whereYear('donation_date', now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray(),
            ],
        ];

        // Journal stats
        $journalStats = [
            'issues' => [
                'total' => JournalIssue::count(),
                'published' => JournalIssue::where('status', 'published')->count(),
            ],
            'submissions' => [
                'total' => Submission::count(),
                'by_status' => Submission::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'this_year' => Submission::whereYear('created_at', now()->year)->count(),
                'by_month' => Submission::select(
                    DB::raw("to_char(created_at, 'YYYY-MM') as month"),
                    DB::raw('count(*) as total')
                )
                    ->whereYear('created_at', now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray(),
            ],
            'reviews' => [
                'total' => Review::count(),
                'by_status' => Review::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'by_recommendation' => Review::whereNotNull('recommendation')
                    ->select('recommendation', DB::raw('count(*) as count'))
                    ->groupBy('recommendation')
                    ->pluck('count', 'recommendation')
                    ->toArray(),
                'overdue' => Review::where('due_date', '<', now())
                    ->whereIn('status', ['invited', 'accepted'])->count(),
                'avg_review_time_days' => Review::whereNotNull('completed_at')
                    ->whereNotNull('responded_at')
                    ->selectRaw('AVG(EXTRACT(day FROM (completed_at - responded_at))) as avg_days')
                    ->value('avg_days'),
            ],
        ];

        // Content stats
        $contentStats = [
            'articles' => [
                'total' => Article::count(),
                'published' => Article::where('status', 'published')->count(),
                'by_status' => Article::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
            ],
            'events' => [
                'total' => Event::count(),
                'upcoming' => Event::where('start_date', '>=', now())->count(),
                'past' => Event::where('end_date', '<', now())->count(),
            ],
        ];

        return view('admin.settings.statistics', compact('stats', 'journalStats', 'contentStats'));
    }
}
