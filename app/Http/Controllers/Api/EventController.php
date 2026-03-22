<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * List all events with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 15), 50);

        $events = Event::query()
            ->where('is_published', true)
            ->orderBy('start_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $events->map(fn ($event) => $this->formatEvent($event)),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ],
        ]);
    }

    /**
     * List upcoming events
     */
    public function upcoming(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 10), 30);

        $events = Event::query()
            ->where('is_published', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $events->map(fn ($event) => $this->formatEvent($event)),
            'count' => $events->count(),
        ]);
    }

    /**
     * Show a single event by slug
     */
    public function show(Event $event): JsonResponse
    {
        if (!$event->is_published) {
            abort(404);
        }

        return response()->json([
            'data' => $this->formatEvent($event, true),
        ]);
    }

    /**
     * Format event for API response
     */
    private function formatEvent(Event $event, bool $includeDescription = false): array
    {
        $data = [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'excerpt' => $event->excerpt,
            'location' => $event->location,
            'image' => $event->image ? asset('storage/' . $event->image) : null,
            'start_date' => $event->start_date?->toISOString(),
            'end_date' => $event->end_date?->toISOString(),
            'is_free' => $event->is_free,
            'registration_url' => $event->registration_url,
        ];

        if ($includeDescription) {
            $data['description'] = $event->description;
        }

        return $data;
    }
}
