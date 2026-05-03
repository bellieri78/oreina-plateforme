<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MenuItemController extends Controller
{
    public function index()
    {
        $headerItems = MenuItem::query()
            ->where('location', 'header')
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        $footerItems = MenuItem::query()
            ->where('location', 'footer')
            ->whereNull('parent_id')
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        return view('admin.menus.index', compact('headerItems', 'footerItems'));
    }

    public function create(Request $request)
    {
        $defaultLocation = $request->query('location', 'header');
        $availableParents = MenuItem::query()
            ->where('location', $defaultLocation)
            ->whereNull('parent_id')
            ->orderBy('label')
            ->get();

        return view('admin.menus.create', [
            'menuItem' => null,
            'defaultLocation' => $defaultLocation,
            'availableParents' => $availableParents,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $this->applyParentRules($validated, null);

        MenuItem::create($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Item de menu créé.');
    }

    public function edit(MenuItem $menu)
    {
        $availableParents = MenuItem::query()
            ->where('location', $menu->location)
            ->whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('label')
            ->get();

        return view('admin.menus.edit', [
            'menuItem' => $menu,
            'availableParents' => $availableParents,
        ]);
    }

    public function update(Request $request, MenuItem $menu)
    {
        $validated = $this->validateRequest($request);
        $this->applyParentRules($validated, $menu);

        $menu->update($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Item de menu mis à jour.');
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Item de menu supprimé (et ses sous-items en cascade).');
    }

    public function reorder(MenuItem $menu, string $direction)
    {
        $sibling = MenuItem::query()
            ->where('location', $menu->location)
            ->where('parent_id', $menu->parent_id)
            ->when($direction === 'up', fn ($q) => $q->where('sort_order', '<', $menu->sort_order)->orderByDesc('sort_order'))
            ->when($direction === 'down', fn ($q) => $q->where('sort_order', '>', $menu->sort_order)->orderBy('sort_order'))
            ->first();

        if ($sibling) {
            $tmp = $menu->sort_order;
            $menu->update(['sort_order' => $sibling->sort_order]);
            $sibling->update(['sort_order' => $tmp]);
        }

        return redirect()->route('admin.menus.index');
    }

    private function validateRequest(Request $request): array
    {
        $validated = $request->validate([
            'label'           => 'required|string|max:255',
            'location'        => ['required', 'in:header,footer'],
            'parent_id'       => 'nullable|exists:menu_items,id',
            'url'             => 'required|string|max:500',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'boolean',
            'open_in_new_tab' => 'boolean',
        ]);

        $validated['is_active']       = $request->has('is_active');
        $validated['open_in_new_tab'] = $request->has('open_in_new_tab');
        $validated['sort_order']      = $validated['sort_order'] ?? 0;

        return $validated;
    }

    private function applyParentRules(array &$validated, ?MenuItem $current): void
    {
        // Footer est plat : pas de sous-items
        if ($validated['location'] === 'footer') {
            $validated['parent_id'] = null;
            return;
        }

        if (empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
            return;
        }

        $parent = MenuItem::find($validated['parent_id']);
        if (! $parent) {
            $validated['parent_id'] = null;
            return;
        }

        // Le parent choisi est lui-même déjà un sous-item → interdit (limite 2 niveaux)
        if ($parent->parent_id !== null) {
            throw ValidationException::withMessages([
                'parent_id' => 'Le parent choisi est déjà un sous-item (limite à 2 niveaux).',
            ]);
        }

        if ($parent->location !== $validated['location']) {
            throw ValidationException::withMessages([
                'parent_id' => 'Le parent doit être dans la même localisation.',
            ]);
        }

        // Si on édite un item qui a déjà des enfants, il ne peut pas devenir lui-même enfant
        if ($current && $current->exists && $current->children()->exists()) {
            throw ValidationException::withMessages([
                'parent_id' => 'Cet item a déjà des sous-items, il ne peut pas devenir lui-même un sous-item.',
            ]);
        }
    }
}
