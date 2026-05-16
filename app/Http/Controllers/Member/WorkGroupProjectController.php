<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\WorkGroup;
use App\Models\WorkGroupProject;
use Illuminate\Http\Request;

class WorkGroupProjectController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys(config('work_group_projects.statuses'))),
            'deliverable_url' => 'nullable|url|max:500',
        ];
    }

    public function store(Request $request, WorkGroup $workGroup)
    {
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $workGroup->projects()->create($request->validate($this->rules()));

        return back()->with('success', 'Projet créé.');
    }

    public function update(Request $request, WorkGroup $workGroup, WorkGroupProject $project)
    {
        abort_unless($project->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $project->update($request->validate($this->rules()));

        return back()->with('success', 'Projet mis à jour.');
    }

    public function destroy(Request $request, WorkGroup $workGroup, WorkGroupProject $project)
    {
        abort_unless($project->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $project->delete();

        return back()->with('success', 'Projet supprimé.');
    }
}
