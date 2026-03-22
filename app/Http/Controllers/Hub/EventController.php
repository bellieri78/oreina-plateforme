<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::published()
            ->upcoming()
            ->paginate(6);

        $pastEvents = Event::published()
            ->where('start_date', '<', now())
            ->orderBy('start_date', 'desc')
            ->take(3)
            ->get();

        $eventTypes = [
            'sortie' => 'Sorties terrain',
            'conference' => 'Conférences',
            'atelier' => 'Ateliers',
            'reunion' => 'Réunions',
            'exposition' => 'Expositions',
        ];

        return view('hub.events.index', compact('upcomingEvents', 'pastEvents', 'eventTypes'));
    }

    public function show(Event $event)
    {
        if ($event->status !== 'published') {
            abort(404);
        }

        $relatedEvents = Event::published()
            ->upcoming()
            ->where('id', '!=', $event->id)
            ->where('event_type', $event->event_type)
            ->take(2)
            ->get();

        return view('hub.events.show', compact('event', 'relatedEvents'));
    }
}
