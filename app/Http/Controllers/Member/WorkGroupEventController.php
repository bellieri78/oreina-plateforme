<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\WorkGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkGroupEventController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'event_type' => 'nullable|string|max:100',
            'mode' => 'required|in:onsite,online',
            'location_name' => 'nullable|string|max:255|required_if:mode,onsite',
            'location_address' => 'nullable|string|max:255',
            'location_city' => 'nullable|string|max:100',
            'meeting_url' => 'nullable|url|max:500|required_if:mode,online',
            'description' => 'nullable|string',
        ];
    }

    private function payload(array $data, WorkGroup $workGroup, int $userId): array
    {
        $onsite = $data['mode'] === 'onsite';

        return [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'event_type' => $data['event_type'] ?? 'reunion',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'location_name' => $onsite ? ($data['location_name'] ?? null) : null,
            'location_address' => $onsite ? ($data['location_address'] ?? null) : null,
            'location_city' => $onsite ? ($data['location_city'] ?? null) : null,
            'meeting_url' => $onsite ? null : ($data['meeting_url'] ?? null),
            'visibility' => Event::VIS_GROUP,
            'organizer_id' => $userId,
            'status' => 'published',
            'published_at' => now(),
        ];
    }

    public function store(Request $request, WorkGroup $workGroup)
    {
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $data = $request->validate($this->rules());

        $payload = $this->payload($data, $workGroup, $request->user()->id);
        $payload['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6));

        $workGroup->events()->create($payload);

        return redirect(route('member.work-groups.show', $workGroup).'?tab=evenements')
            ->with('success', 'Réunion planifiée.');
    }

    private function eventsTabRedirect(WorkGroup $workGroup, string $message)
    {
        return redirect(route('member.work-groups.show', $workGroup).'?tab=evenements')
            ->with('success', $message);
    }

    public function update(Request $request, WorkGroup $workGroup, Event $event)
    {
        abort_unless($event->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $data = $request->validate($this->rules());
        $event->update($this->payload($data, $workGroup, $event->organizer_id ?? $request->user()->id));

        return $this->eventsTabRedirect($workGroup, 'Réunion mise à jour.');
    }

    public function destroy(Request $request, WorkGroup $workGroup, Event $event)
    {
        abort_unless($event->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $event->delete();

        return $this->eventsTabRedirect($workGroup, 'Réunion supprimée.');
    }
}
