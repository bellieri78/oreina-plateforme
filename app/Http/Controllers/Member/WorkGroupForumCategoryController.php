<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\WorkGroup;
use App\Models\WorkGroupForumCategory;
use Illuminate\Http\Request;

class WorkGroupForumCategoryController extends Controller
{
    public function store(Request $request, WorkGroup $workGroup)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
        ]);

        WorkGroupForumCategory::create([
            'work_group_id' => $workGroup->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'position' => $data['position'] ?? 0,
        ]);

        return back()->with('success', 'Catégorie créée.');
    }

    public function update(Request $request, WorkGroup $workGroup, WorkGroupForumCategory $category)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($category->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
        ]);

        $category->update($data);

        return back()->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Request $request, WorkGroup $workGroup, WorkGroupForumCategory $category)
    {
        abort_unless($workGroup->has_forum, 404);
        abort_unless($category->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $category->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }
}
