<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class StructureController extends Controller
{
    public function index(Request $request)
    {
        $query = Structure::with(['parent', 'responsable', 'children'])
            ->withCount(['activeMembers', 'children']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        // Filter by parent
        if ($request->filled('parent')) {
            if ($request->get('parent') === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->get('parent'));
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%")
                  ->orWhere('city', 'ilike', "%{$search}%")
                  ->orWhere('departement_code', 'ilike', "%{$search}%");
            });
        }

        // Build tree view or flat list (default: grid)
        $viewMode = $request->get('view', 'grid');

        if ($viewMode === 'tree') {
            // Get root structures for tree view
            $structures = Structure::roots()
                ->with(['allChildren', 'responsable'])
                ->withCount(['activeMembers', 'children'])
                ->ordered()
                ->get();
        } else {
            $structures = $query->ordered()->paginate(25)->withQueryString();
        }

        // Stats
        $stats = [
            'total' => Structure::count(),
            'national' => Structure::ofType('national')->count(),
            'regional' => Structure::ofType('regional')->count(),
            'departemental' => Structure::ofType('departemental')->count(),
            'local' => Structure::ofType('local')->count(),
            'active' => Structure::active()->count(),
        ];

        // Filter options
        $parentStructures = Structure::active()->ordered()->pluck('name', 'id');

        return view('admin.structures.index', compact(
            'structures',
            'stats',
            'parentStructures',
            'viewMode'
        ));
    }

    public function create()
    {
        $parentStructures = Structure::getTreeForSelect();
        $members = Member::orderBy('last_name')->orderBy('first_name')->get();

        return view('admin.structures.create', compact('parentStructures', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:structures,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:national,regional,departemental,local',
            'parent_id' => 'nullable|exists:structures,id',
            'description' => 'nullable|string',
            'departement_code' => 'nullable|string|max:3',
            'region' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'responsable_id' => 'nullable|exists:members,id',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $structure = Structure::create($validated);

        return redirect()
            ->route('admin.structures.show', $structure)
            ->with('success', 'Structure creee avec succes.');
    }

    public function show(Structure $structure)
    {
        $structure->load(['parent', 'children.responsable', 'responsable', 'activeMembers']);

        return view('admin.structures.show', compact('structure'));
    }

    public function edit(Structure $structure)
    {
        $parentStructures = Structure::getTreeForSelect();
        // Remove current structure and its descendants from parent options
        $descendants = $structure->getDescendants()->pluck('id')->toArray();
        $descendants[] = $structure->id;
        $parentStructures = collect($parentStructures)->except($descendants)->toArray();

        $members = Member::orderBy('last_name')->orderBy('first_name')->get();

        return view('admin.structures.edit', compact('structure', 'parentStructures', 'members'));
    }

    public function update(Request $request, Structure $structure)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('structures')->ignore($structure->id)],
            'name' => 'required|string|max:255',
            'type' => 'required|in:national,regional,departemental,local',
            'parent_id' => 'nullable|exists:structures,id',
            'description' => 'nullable|string',
            'departement_code' => 'nullable|string|max:3',
            'region' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'responsable_id' => 'nullable|exists:members,id',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Prevent circular reference
        if (isset($validated['parent_id']) && $validated['parent_id']) {
            if ($validated['parent_id'] == $structure->id) {
                return back()->with('error', 'Une structure ne peut pas etre son propre parent.');
            }
            $parent = Structure::find($validated['parent_id']);
            if ($parent && $structure->isAncestorOf($parent)) {
                return back()->with('error', 'Impossible: reference circulaire detectee.');
            }
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $structure->update($validated);

        return redirect()
            ->route('admin.structures.show', $structure)
            ->with('success', 'Structure mise a jour.');
    }

    public function destroy(Structure $structure)
    {
        // Check if structure has children
        if ($structure->children()->count() > 0) {
            return redirect()->route('admin.structures.index')
                ->with('error', 'Impossible de supprimer une structure avec des sous-structures.');
        }

        // Remove member associations
        $structure->members()->detach();

        $structure->delete();

        return redirect()
            ->route('admin.structures.index')
            ->with('success', 'Structure supprimee.');
    }

    /**
     * Show members management page for a structure
     */
    public function members(Structure $structure)
    {
        $structure->load('activeMembers');

        // Get members not in this structure
        $existingIds = $structure->members()->pluck('members.id')->toArray();
        $availableMembers = Member::whereNotIn('id', $existingIds)
            ->active()
            ->orderBy('last_name')
            ->get();

        return view('admin.structures.members', compact('structure', 'availableMembers'));
    }

    /**
     * Add a member to a structure
     */
    public function addMember(Request $request, Structure $structure)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'role' => 'nullable|string|max:50',
            'joined_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Check if already a member
        if ($structure->members()->where('member_id', $validated['member_id'])->exists()) {
            return back()->with('error', 'Ce contact est deja membre de cette structure.');
        }

        $structure->members()->attach($validated['member_id'], [
            'role' => $validated['role'] ?? Structure::ROLE_MEMBRE,
            'joined_at' => $validated['joined_at'] ?? now()->toDateString(),
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Membre ajoute a la structure.');
    }

    /**
     * Update member role in structure
     */
    public function updateMember(Request $request, Structure $structure, Member $member)
    {
        $validated = $request->validate([
            'role' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $structure->members()->updateExistingPivot($member->id, $validated);

        return back()->with('success', 'Role mis a jour.');
    }

    /**
     * Remove a member from a structure
     */
    public function removeMember(Structure $structure, Member $member)
    {
        $structure->members()->updateExistingPivot($member->id, [
            'left_at' => now()->toDateString(),
        ]);

        return back()->with('success', 'Membre retire de la structure.');
    }

    /**
     * Export structures to CSV
     */
    public function export(Request $request)
    {
        $query = Structure::with(['parent', 'responsable'])
            ->withCount('activeMembers');

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $structures = $query->ordered()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="structures_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['Code', 'Nom', 'Type', 'Parent', 'Departement', 'Ville', 'Email', 'Responsable', 'Membres', 'Statut'];

        $callback = function () use ($structures, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($structures as $s) {
                fputcsv($file, [
                    $s->code,
                    $s->name,
                    $s->type_label,
                    $s->parent?->name ?? '-',
                    $s->departement_code ?? '-',
                    $s->city ?? '-',
                    $s->email ?? '-',
                    $s->responsable?->full_name ?? '-',
                    $s->active_members_count,
                    $s->is_active ? 'Actif' : 'Inactif',
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
