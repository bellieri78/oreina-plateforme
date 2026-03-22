@extends('layouts.admin')
@section('title', $user->name)
@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}">Utilisateurs</a>
    <span>/</span>
    <span>{{ $user->name }}</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div>
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">{{ $user->name }}</h3>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary">Modifier</a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Informations</h4>
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Email :</span>
                                <a href="mailto:{{ $user->email }}" style="color: var(--color-primary);">{{ $user->email }}</a>
                            </div>
                            @if($user->phone)
                                <div style="margin-bottom: 0.75rem;">
                                    <span style="color: #6b7280;">Telephone :</span>
                                    <span>{{ $user->phone }}</span>
                                </div>
                            @endif
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Inscrit le :</span>
                                <span>{{ $user->created_at->format('d/m/Y a H:i') }}</span>
                            </div>
                        </div>
                        <div>
                            <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Compte</h4>
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Role :</span>
                                @php
                                    $roleColors = [
                                        'user' => 'secondary',
                                        'author' => 'info',
                                        'reviewer' => 'warning',
                                        'editor' => 'primary',
                                        'admin' => 'danger',
                                    ];
                                @endphp
                                <span class="badge badge-{{ $roleColors[$user->role] ?? 'secondary' }}">
                                    {{ \App\Models\User::getRoles()[$user->role] ?? $user->role }}
                                </span>
                            </div>
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Statut :</span>
                                @if($user->is_active)
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-secondary">Inactif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activite</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align: center;">
                        <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--color-primary);">{{ $user->articles_count }}</div>
                            <div style="font-size: 0.875rem; color: #6b7280;">Article(s)</div>
                        </div>
                        <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--color-primary);">{{ $user->submissions_count }}</div>
                            <div style="font-size: 0.875rem; color: #6b7280;">Soumission(s)</div>
                        </div>
                        <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--color-primary);">{{ $user->assigned_reviews_count }}</div>
                            <div style="font-size: 0.875rem; color: #6b7280;">Review(s)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            {{-- Quick Actions --}}
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Actions rapides</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @if($user->id !== auth()->id())
                            @if($user->is_active)
                                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="name" value="{{ $user->name }}">
                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <input type="hidden" name="is_active" value="0">
                                    <button type="submit" class="btn btn-warning" style="width: 100%;" onclick="return confirm('Desactiver ce compte ?')">
                                        Desactiver le compte
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="name" value="{{ $user->name }}">
                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <input type="hidden" name="is_active" value="1">
                                    <button type="submit" class="btn btn-success" style="width: 100%;">
                                        Activer le compte
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if($user->isReviewer())
                            <a href="{{ route('admin.reviews.index') }}?reviewer={{ $user->id }}" class="btn btn-secondary" style="width: 100%;">
                                Voir ses reviews
                            </a>
                        @endif

                        @if($user->isAuthor())
                            <a href="{{ route('admin.submissions.index') }}?author={{ $user->id }}" class="btn btn-secondary" style="width: 100%;">
                                Voir ses soumissions
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Permissions</h3>
                    @if(!$user->isAdmin())
                        <a href="{{ route('admin.users.permissions', $user) }}" class="btn btn-secondary btn-sm">
                            Gerer
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div style="font-size: 0.875rem; color: #4b5563;">
                        @if($user->isAdmin())
                            <div style="padding: 0.75rem; background: #fef3c7; border-radius: 0.375rem; color: #92400e; margin-bottom: 0.5rem;">
                                <strong>Administrateur</strong> - Acces complet
                            </div>
                            <p style="color: #6b7280;">Les administrateurs ont automatiquement toutes les permissions.</p>
                        @else
                            @php
                                $userPermissions = $user->permissions()->get()->groupBy('module');
                                $moduleLabels = \App\Models\Permission::getModuleLabels();
                            @endphp
                            @if($userPermissions->isEmpty())
                                <p style="color: #6b7280;">Aucune permission specifique.</p>
                            @else
                                @foreach($userPermissions as $module => $perms)
                                    <div style="margin-bottom: 0.5rem;">
                                        <strong>{{ $moduleLabels[$module] ?? $module }}</strong>
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.25rem; margin-top: 0.25rem;">
                                            @foreach($perms as $perm)
                                                <span class="badge badge-info" style="font-size: 0.7rem;">{{ $perm->action }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
