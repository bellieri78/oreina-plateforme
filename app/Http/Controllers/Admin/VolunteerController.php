<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Structure;
use App\Models\VolunteerActivity;
use App\Models\VolunteerActivityType;
use App\Models\VolunteerParticipation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VolunteerController extends Controller
{
    /**
     * Dashboard overview
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));

        // Stats
        $stats = [
            'total_activities' => VolunteerActivity::inYear($year)->count(),
            'completed' => VolunteerActivity::inYear($year)->ofStatus('completed')->count(),
            'upcoming' => VolunteerActivity::upcoming()->count(),
            'total_hours' => VolunteerParticipation::attended()
                ->whereHas('activity', fn($q) => $q->whereYear('activity_date', $year))
                ->sum('hours_worked'),
            'total_volunteers' => VolunteerParticipation::attended()
                ->whereHas('activity', fn($q) => $q->whereYear('activity_date', $year))
                ->distinct('member_id')
                ->count('member_id'),
        ];

        // Upcoming activities
        $upcomingActivities = VolunteerActivity::upcoming()
            ->with(['activityType', 'organizer', 'structure'])
            ->withCount('confirmedParticipants')
            ->take(5)
            ->get();

        // Recent activities
        $recentActivities = VolunteerActivity::past()
            ->with(['activityType', 'organizer'])
            ->withCount('attendedParticipants')
            ->take(5)
            ->get();

        // Top volunteers
        $topVolunteers = Member::select('members.*')
            ->join('volunteer_participations', 'members.id', '=', 'volunteer_participations.member_id')
            ->join('volunteer_activities', 'volunteer_participations.volunteer_activity_id', '=', 'volunteer_activities.id')
            ->where('volunteer_participations.status', 'attended')
            ->whereYear('volunteer_activities.activity_date', $year)
            ->groupBy('members.id')
            ->selectRaw('SUM(volunteer_participations.hours_worked) as total_hours')
            ->orderByDesc('total_hours')
            ->take(10)
            ->get();

        // Available years
        $years = VolunteerActivity::selectRaw('EXTRACT(YEAR FROM activity_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }

        return view('admin.volunteer.index', compact(
            'stats',
            'upcomingActivities',
            'recentActivities',
            'topVolunteers',
            'years',
            'year'
        ));
    }

    /**
     * List all activities
     */
    public function activities(Request $request)
    {
        $query = VolunteerActivity::with(['activityType', 'organizer', 'structure'])
            ->withCount(['participants', 'attendedParticipants']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('location', 'ilike', "%{$search}%")
                  ->orWhere('city', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('activity_type_id', $request->get('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('structure')) {
            $query->where('structure_id', $request->get('structure'));
        }

        if ($request->filled('year')) {
            $query->whereYear('activity_date', $request->get('year'));
        }

        if ($request->filled('date_from')) {
            $query->where('activity_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('activity_date', '<=', $request->get('date_to'));
        }

        // Sorting
        $sort = $request->get('sort', 'date');
        switch ($sort) {
            case 'title':
                $query->orderBy('title');
                break;
            case 'participants':
                $query->orderByDesc('participants_count');
                break;
            default:
                $query->orderByDesc('activity_date');
        }

        $activities = $query->paginate(12)->withQueryString();

        // Filter options
        $activityTypes = VolunteerActivityType::active()->ordered()->pluck('name', 'id');
        $structures = Structure::active()->ordered()->pluck('name', 'id');
        $years = VolunteerActivity::selectRaw('EXTRACT(YEAR FROM activity_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Stats
        $upcomingActivities = VolunteerActivity::where('activity_date', '>=', today())->get();
        $totalHours = $upcomingActivities->sum(function ($activity) {
            return $activity->duration ?? 0;
        });

        $stats = [
            'upcoming' => $upcomingActivities->count(),
            'total_volunteers' => \App\Models\VolunteerParticipation::distinct('member_id')->count(),
            'total_hours' => round($totalHours),
            'fill_rate' => $this->calculateFillRate(),
        ];

        return view('admin.volunteer.activities', compact(
            'activities',
            'activityTypes',
            'structures',
            'years',
            'stats'
        ));
    }

    /**
     * Calculate average fill rate for upcoming activities
     */
    private function calculateFillRate(): int
    {
        $upcomingWithMax = VolunteerActivity::where('activity_date', '>=', today())
            ->whereNotNull('max_participants')
            ->where('max_participants', '>', 0)
            ->withCount('participants')
            ->get();

        if ($upcomingWithMax->isEmpty()) {
            return 0;
        }

        $totalRate = $upcomingWithMax->sum(function ($activity) {
            return min(100, round(($activity->participants_count / $activity->max_participants) * 100));
        });

        return round($totalRate / $upcomingWithMax->count());
    }

    /**
     * Create activity form
     */
    public function create()
    {
        $activityTypes = VolunteerActivityType::active()->ordered()->get();
        $structures = Structure::getTreeForSelect();
        $members = Member::active()->orderBy('last_name')->get();

        return view('admin.volunteer.create', compact('activityTypes', 'structures', 'members'));
    }

    /**
     * Store new activity
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_type_id' => 'required|exists:volunteer_activity_types,id',
            'structure_id' => 'nullable|exists:structures,id',
            'activity_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'organizer_id' => 'nullable|exists:members,id',
            'max_participants' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'planned';

        $activity = VolunteerActivity::create($validated);

        return redirect()
            ->route('admin.volunteer.show', $activity)
            ->with('success', 'Activite creee avec succes.');
    }

    /**
     * Show activity details
     */
    public function show(VolunteerActivity $activity)
    {
        $activity->load(['activityType', 'organizer', 'structure', 'participations.member']);

        // Get members not yet participating
        $participantIds = $activity->participants()->pluck('members.id');
        $availableMembers = Member::active()
            ->whereNotIn('id', $participantIds)
            ->orderBy('last_name')
            ->get();

        return view('admin.volunteer.show', compact('activity', 'availableMembers'));
    }

    /**
     * Edit activity form
     */
    public function edit(VolunteerActivity $activity)
    {
        $activityTypes = VolunteerActivityType::active()->ordered()->get();
        $structures = Structure::getTreeForSelect();
        $members = Member::active()->orderBy('last_name')->get();

        return view('admin.volunteer.edit', compact('activity', 'activityTypes', 'structures', 'members'));
    }

    /**
     * Update activity
     */
    public function update(Request $request, VolunteerActivity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_type_id' => 'required|exists:volunteer_activity_types,id',
            'structure_id' => 'nullable|exists:structures,id',
            'activity_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'organizer_id' => 'nullable|exists:members,id',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'max_participants' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $activity->update($validated);

        return redirect()
            ->route('admin.volunteer.show', $activity)
            ->with('success', 'Activite mise a jour.');
    }

    /**
     * Delete activity
     */
    public function destroy(VolunteerActivity $activity)
    {
        $activity->participants()->detach();
        $activity->delete();

        return redirect()
            ->route('admin.volunteer.activities')
            ->with('success', 'Activite supprimee.');
    }

    /**
     * Add participant to activity
     */
    public function addParticipant(Request $request, VolunteerActivity $activity)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'status' => 'required|in:registered,confirmed,attended',
            'hours_worked' => 'nullable|numeric|min:0',
        ]);

        if ($activity->participants()->where('member_id', $validated['member_id'])->exists()) {
            return back()->with('error', 'Ce benevole est deja inscrit.');
        }

        $activity->participants()->attach($validated['member_id'], [
            'status' => $validated['status'],
            'hours_worked' => $validated['hours_worked'],
        ]);

        return back()->with('success', 'Benevole ajoute.');
    }

    /**
     * Update participant status
     */
    public function updateParticipant(Request $request, VolunteerActivity $activity, Member $member)
    {
        $validated = $request->validate([
            'status' => 'required|in:registered,confirmed,attended,absent,cancelled',
            'hours_worked' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $activity->participants()->updateExistingPivot($member->id, $validated);

        return back()->with('success', 'Participation mise a jour.');
    }

    /**
     * Remove participant from activity
     */
    public function removeParticipant(VolunteerActivity $activity, Member $member)
    {
        $activity->participants()->detach($member->id);

        return back()->with('success', 'Benevole retire.');
    }

    /**
     * Mark all participants as attended (bulk)
     */
    public function markAllAttended(Request $request, VolunteerActivity $activity)
    {
        $hours = $request->get('hours', $activity->duration);

        $activity->participants()
            ->whereIn('volunteer_participations.status', ['registered', 'confirmed'])
            ->each(function ($participant) use ($hours, $activity) {
                $activity->participants()->updateExistingPivot($participant->id, [
                    'status' => 'attended',
                    'hours_worked' => $hours,
                ]);
            });

        $activity->update(['status' => 'completed']);

        return back()->with('success', 'Tous les participants ont ete marques presents.');
    }

    /**
     * Export activities to CSV
     */
    public function export(Request $request)
    {
        $query = VolunteerActivity::with(['activityType', 'organizer', 'structure'])
            ->withCount(['participants', 'attendedParticipants']);

        if ($request->filled('year')) {
            $query->whereYear('activity_date', $request->get('year'));
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $activities = $query->orderByDesc('activity_date')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="benevolat_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['Date', 'Titre', 'Type', 'Structure', 'Lieu', 'Organisateur', 'Statut', 'Participants', 'Presents', 'Heures'];

        $callback = function () use ($activities, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($activities as $a) {
                fputcsv($file, [
                    $a->activity_date->format('d/m/Y'),
                    $a->title,
                    $a->activityType?->name ?? '-',
                    $a->structure?->name ?? '-',
                    $a->location . ($a->city ? ', ' . $a->city : ''),
                    $a->organizer?->full_name ?? '-',
                    $a->status_label,
                    $a->participants_count,
                    $a->attended_participants_count,
                    $a->total_hours,
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Volunteer report for a member
     */
    public function memberReport(Request $request, Member $member)
    {
        $year = $request->get('year', date('Y'));

        // Get available years
        $years = $member->volunteerParticipations()
            ->join('volunteer_activities', 'volunteer_participations.volunteer_activity_id', '=', 'volunteer_activities.id')
            ->selectRaw('EXTRACT(YEAR FROM volunteer_activities.activity_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }

        // Participations for the selected year
        $participations = $member->volunteerParticipations()
            ->with(['activity.activityType'])
            ->whereHas('activity', fn($q) => $q->whereYear('activity_date', $year))
            ->orderByDesc('created_at')
            ->get();

        // Stats for the selected year
        $stats = [
            'total_activities' => $participations->count(),
            'attended' => $participations->where('status', 'attended')->count(),
            'total_hours' => $participations->where('status', 'attended')->sum('hours_worked'),
            'upcoming' => $participations->filter(fn($p) => $p->activity->activity_date >= now()->startOfDay()
                && in_array($p->status, ['registered', 'confirmed']))->count(),
        ];

        // Breakdown by activity type
        $typeBreakdown = $participations->where('status', 'attended')
            ->groupBy(fn($p) => $p->activity->activityType?->id ?? 0)
            ->map(fn($group) => [
                'name' => $group->first()->activity->activityType?->name ?? 'Autre',
                'color' => $group->first()->activity->activityType?->color ?? '#ccc',
                'count' => $group->count(),
                'hours' => $group->sum('hours_worked'),
            ])
            ->values();

        // Monthly breakdown
        $monthlyBreakdown = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyBreakdown[$m] = ['count' => 0, 'hours' => 0];
        }
        foreach ($participations->where('status', 'attended') as $p) {
            $month = $p->activity->activity_date->month;
            $monthlyBreakdown[$month]['count']++;
            $monthlyBreakdown[$month]['hours'] += $p->hours_worked ?? 0;
        }

        // All-time stats
        $allParticipations = $member->volunteerParticipations()
            ->with('activity')
            ->where('status', 'attended')
            ->get();

        $allTimeStats = [
            'total_activities' => $allParticipations->count(),
            'total_hours' => $allParticipations->sum('hours_worked'),
            'years_active' => $allParticipations->pluck('activity.activity_date')
                ->map(fn($d) => $d->year)
                ->unique()
                ->count(),
            'first_activity' => $allParticipations->sortBy('activity.activity_date')
                ->first()?->activity?->activity_date,
        ];

        return view('admin.volunteer.member-report', compact(
            'member',
            'participations',
            'stats',
            'year',
            'years',
            'typeBreakdown',
            'monthlyBreakdown',
            'allTimeStats'
        ));
    }
}
