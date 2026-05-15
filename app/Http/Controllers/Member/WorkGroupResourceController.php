<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\WorkGroup;
use App\Models\WorkGroupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class WorkGroupResourceController extends Controller
{
    public function store(Request $request, WorkGroup $workGroup)
    {
        abort_unless($request->user()->can('create', [WorkGroupResource::class, $workGroup]), 403);

        $data = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(config('work_group_resources.categories'))),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'external_url' => 'nullable|url|max:500',
        ]);

        if (! $request->hasFile('file') && empty($data['external_url'])) {
            throw ValidationException::withMessages([
                'file' => 'Fournissez un fichier OU un lien externe.',
            ]);
        }

        $member = Member::where('user_id', auth()->id())->first();

        $payload = [
            'work_group_id' => $workGroup->id,
            'category' => $data['category'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'added_by_member_id' => $member?->id,
        ];

        if ($request->hasFile('file')) {
            $payload['type'] = 'file';
            $payload['file_path'] = $request->file('file')->store('work-groups/resources', 'public');
        } else {
            $payload['type'] = 'link';
            $payload['external_url'] = $data['external_url'];
        }

        WorkGroupResource::create($payload);

        return back()->with('success', 'Ressource ajoutée.');
    }

    public function destroy(Request $request, WorkGroup $workGroup, WorkGroupResource $resource)
    {
        abort_unless($resource->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('delete', $resource), 403);

        if ($resource->type === 'file' && $resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return back()->with('success', 'Ressource supprimée.');
    }
}
