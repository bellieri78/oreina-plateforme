@extends('layouts.admin')
@section('title', 'Permissions - ' . $user->name)
@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}">Utilisateurs</a>
    <span>/</span>
    <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
    <span>/</span>
    <span>Permissions</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Gerer les permissions de {{ $user->name }}</h3>
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">Retour</a>
        </div>

        @if($user->isAdmin())
            <div class="card-body">
                <div style="padding: 1.5rem; background: #fef3c7; border-radius: 0.5rem; color: #92400e; text-align: center;">
                    <strong>Administrateur</strong>
                    <p style="margin-top: 0.5rem;">Les administrateurs ont automatiquement toutes les permissions. Pas besoin de les configurer.</p>
                </div>
            </div>
        @else
            <form action="{{ route('admin.users.permissions.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <p style="margin-bottom: 1.5rem; color: #6b7280;">
                        Selectionnez les permissions que cet utilisateur doit avoir. Le role actuel est :
                        <span class="badge badge-primary">{{ \App\Models\User::getRoles()[$user->role] ?? $user->role }}</span>
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                        @foreach($permissions as $module => $modulePermissions)
                            <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                                <div style="background: #f3f4f6; padding: 0.75rem 1rem; border-bottom: 1px solid #e5e7eb;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <strong style="color: #374151;">{{ $moduleLabels[$module] ?? $module }}</strong>
                                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #6b7280; cursor: pointer;">
                                            <input type="checkbox" class="select-all-module" data-module="{{ $module }}" style="cursor: pointer;">
                                            Tout
                                        </label>
                                    </div>
                                </div>
                                <div style="padding: 0.75rem 1rem;">
                                    @foreach($modulePermissions as $permission)
                                        <label style="display: flex; align-items: flex-start; gap: 0.5rem; padding: 0.5rem 0; cursor: pointer; border-bottom: 1px solid #f3f4f6;">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission['id'] }}"
                                                   class="permission-{{ $module }}"
                                                   {{ in_array($permission['id'], $userPermissionIds) ? 'checked' : '' }}
                                                   style="margin-top: 0.15rem; cursor: pointer;">
                                            <div>
                                                <span style="font-size: 0.875rem; color: #374151;">{{ $permission['description'] ?? $permission['action'] }}</span>
                                                <span style="display: block; font-size: 0.75rem; color: #9ca3af;">{{ $module }}.{{ $permission['action'] }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card-footer" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les permissions</button>
                </div>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkboxes for a module
    document.querySelectorAll('.select-all-module').forEach(function(selectAll) {
        const module = selectAll.dataset.module;
        const checkboxes = document.querySelectorAll('.permission-' + module);

        // Set initial state
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        selectAll.checked = allChecked;

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(function(cb) {
                cb.checked = selectAll.checked;
            });
        });

        // Update "select all" when individual checkboxes change
        checkboxes.forEach(function(cb) {
            cb.addEventListener('change', function() {
                selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            });
        });
    });
});
</script>
@endpush
