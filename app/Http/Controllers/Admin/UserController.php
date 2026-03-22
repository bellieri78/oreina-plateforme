<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admins' => User::where('role', 'admin')->count(),
            'reviewers' => User::whereIn('role', ['reviewer', 'editor', 'admin'])->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:user,author,reviewer,editor,admin',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Utilisateur cree avec succes.');
    }

    public function show(User $user)
    {
        $user->loadCount(['articles', 'submissions', 'assignedReviews']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:user,author,reviewer,editor,admin',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Only update password if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis a jour.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Check if user has related data
        if ($user->articles()->count() > 0 || $user->submissions()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cet utilisateur a des articles ou soumissions associes.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprime.');
    }

    public function export(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $users = $query->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="utilisateurs_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Nom', 'Email', 'Telephone', 'Role', 'Actif', 'Cree le'];

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($users as $u) {
                fputcsv($file, [
                    $u->id,
                    $u->name,
                    $u->email,
                    $u->phone ?? '-',
                    User::getRoles()[$u->role] ?? $u->role,
                    $u->is_active ? 'Oui' : 'Non',
                    $u->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|string']);

        $ids = explode(',', $request->get('ids'));

        // Remove current user from the list
        $ids = array_filter($ids, fn($id) => (int)$id !== auth()->id());

        // Check for users with content
        $usersWithContent = User::whereIn('id', $ids)
            ->where(function ($q) {
                $q->whereHas('articles')
                  ->orWhereHas('submissions');
            })->count();

        if ($usersWithContent > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Certains utilisateurs ont du contenu associe.');
        }

        $deleted = User::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$deleted} utilisateur(s) supprime(s).");
    }

    public function bulkRole(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'role' => 'required|in:user,author,reviewer,editor,admin',
        ]);

        $ids = explode(',', $request->get('ids'));
        $updated = User::whereIn('id', $ids)->update(['role' => $request->get('role')]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$updated} utilisateur(s) mis a jour.");
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'is_active' => 'required|in:0,1',
        ]);

        $ids = explode(',', $request->get('ids'));

        // Prevent deactivating yourself
        $ids = array_filter($ids, fn($id) => (int)$id !== auth()->id());

        $updated = User::whereIn('id', $ids)->update(['is_active' => (bool) $request->get('is_active')]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$updated} utilisateur(s) mis a jour.");
    }

    /**
     * Show permissions form for a user
     */
    public function permissions(User $user)
    {
        // Get all permissions grouped by module
        $permissions = Permission::getGroupedByModule();
        $moduleLabels = Permission::getModuleLabels();

        // Get user's current permissions
        $userPermissionIds = $user->getPermissionIds();

        return view('admin.users.permissions', compact(
            'user',
            'permissions',
            'moduleLabels',
            'userPermissionIds'
        ));
    }

    /**
     * Update permissions for a user
     */
    public function updatePermissions(Request $request, User $user)
    {
        // Admins have all permissions by default, so we shouldn't manage them manually
        if ($user->isAdmin()) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('info', 'Les administrateurs ont automatiquement toutes les permissions.');
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $validated['permissions'] ?? [];
        $user->syncPermissions($permissionIds);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Permissions mises a jour avec succes.');
    }
}
