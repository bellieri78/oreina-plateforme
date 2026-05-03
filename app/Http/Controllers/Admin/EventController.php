<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('organizer');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('location_city', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('period')) {
            if ($request->get('period') === 'upcoming') {
                $query->where('start_date', '>=', now());
            } else {
                $query->where('start_date', '<', now());
            }
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->get('event_type'));
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->get('year'));
        }

        // Sorting
        $sortField = $request->get('sort', 'start_date');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['start_date', 'created_at', 'title'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('start_date', 'desc');
        }

        $events = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Event::count(),
            'upcoming' => Event::where('start_date', '>=', now())->count(),
            'published' => Event::where('status', 'published')->count(),
            'this_month' => Event::whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)->count(),
        ];

        $eventTypes = Event::whereNotNull('event_type')->distinct()->pluck('event_type')->sort();
        $years = Event::selectRaw('EXTRACT(YEAR FROM start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.events.index', compact('events', 'stats', 'eventTypes', 'years'));
    }

    public function create()
    {
        $organizers = User::orderBy('name')->get();
        return view('admin.events.create', compact('organizers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:events,slug',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'event_type' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location_name' => 'nullable|string|max:255',
            'location_address' => 'nullable|string|max:255',
            'location_city' => 'nullable|string|max:100',
            'max_participants' => 'nullable|integer|min:1',
            'registration_required' => 'boolean',
            'registration_url' => 'nullable|url',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,published',
            'organizer_id' => 'nullable|exists:users,id',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|max:5120',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (empty($validated['organizer_id'])) {
            $validated['organizer_id'] = auth()->id();
        }

        $validated['registration_required'] = $request->boolean('registration_required');

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('events/images', 'public');
        }

        $event = Event::create($validated);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Evenement cree avec succes.');
    }

    public function show(Event $event)
    {
        $event->load('organizer');
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $organizers = User::orderBy('name')->get();
        return view('admin.events.edit', compact('event', 'organizers'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:events,slug,' . $event->id,
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'event_type' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location_name' => 'nullable|string|max:255',
            'location_address' => 'nullable|string|max:255',
            'location_city' => 'nullable|string|max:100',
            'max_participants' => 'nullable|integer|min:1',
            'registration_required' => 'boolean',
            'registration_url' => 'nullable|url',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,published',
            'organizer_id' => 'nullable|exists:users,id',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|max:5120',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['registration_required'] = $request->boolean('registration_required');

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            if ($event->featured_image) {
                Storage::disk('public')->delete($event->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('events/images', 'public');
        }

        $event->update($validated);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Evenement mis a jour.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Evenement supprime.');
    }

    /**
     * Export events to CSV
     */
    public function export(Request $request)
    {
        $query = Event::with('organizer');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('location_city', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('period')) {
            if ($request->get('period') === 'upcoming') {
                $query->where('start_date', '>=', now());
            } else {
                $query->where('start_date', '<', now());
            }
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $events = $query->orderBy('start_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="evenements_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Titre', 'Type', 'Date debut', 'Date fin', 'Lieu', 'Ville', 'Statut', 'Inscription', 'Prix'];

        $callback = function () use ($events, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($events as $e) {
                fputcsv($file, [
                    $e->id,
                    $e->title,
                    $e->event_type ?? '-',
                    $e->start_date->format('d/m/Y H:i'),
                    $e->end_date?->format('d/m/Y H:i') ?? '-',
                    $e->location_name ?? '-',
                    $e->location_city ?? '-',
                    $e->status,
                    $e->registration_required ? 'Oui' : 'Non',
                    $e->price ? number_format($e->price, 2, ',', ' ') . ' EUR' : 'Gratuit',
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Bulk delete events
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|string']);

        $ids = explode(',', $request->get('ids'));
        $deleted = Event::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', "{$deleted} evenement(s) supprime(s).");
    }

    /**
     * Bulk update status
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $ids = explode(',', $request->get('ids'));
        $data = ['status' => $request->get('status')];

        if ($request->get('status') === 'published') {
            $data['published_at'] = now();
        }

        $updated = Event::whereIn('id', $ids)->update($data);

        return redirect()
            ->route('admin.events.index')
            ->with('success', "{$updated} evenement(s) mis a jour.");
    }
}
