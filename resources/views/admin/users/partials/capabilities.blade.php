@php
    $currentCaps = $user->capabilities()->pluck('capability')->all();
    $canManage = app(\App\Policies\SubmissionPolicy::class)->manageCapabilities(auth()->user(), $user);
@endphp

<div style="background:#fff;border:2px solid #14b8a6;border-left:6px solid #0d9488;border-radius:0.75rem;box-shadow:0 4px 12px rgba(0,0,0,0.04);padding:1.5rem;margin-top:1.5rem;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem;">
        <div>
            <h2 style="font-size:1.125rem;font-weight:700;color:#16302B;margin:0 0 0.25rem 0;display:flex;align-items:center;gap:0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20" style="color:#0d9488;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                </svg>
                Capacités éditoriales Chersotis
            </h2>
            <p style="font-size:0.8rem;color:#6b7280;margin:0;">
                Formulaire indépendant — utilisez son propre bouton ci-dessous, pas le bouton « Enregistrer » du bloc Utilisateur.
            </p>
        </div>
    </div>

    @if(!$canManage)
        <p style="font-size:0.875rem;color:#6b7280;margin-bottom:1rem;">Vous n'avez pas les droits pour modifier ces capacités.</p>
    @endif

    <form method="POST" action="{{ route('admin.users.capabilities.update', $user) }}" style="display:flex;flex-direction:column;gap:0.6rem;">
        @csrf
        @method('PUT')

        @foreach(\App\Models\EditorialCapability::ALL as $cap)
            <label style="display:flex;align-items:center;gap:0.75rem;cursor:{{ $canManage ? 'pointer' : 'not-allowed' }};">
                <input type="checkbox"
                       name="capabilities[]"
                       value="{{ $cap }}"
                       @checked(in_array($cap, $currentCaps))
                       @disabled(!$canManage)
                       style="width:1rem;height:1rem;accent-color:#0d9488;">
                <span style="font-size:0.9rem;">
                    <strong>{{ \App\Models\EditorialCapability::labels()[$cap] }}</strong>
                    <span style="color:#6b7280;">({{ $cap }})</span>
                </span>
            </label>
        @endforeach

        @if($canManage)
            <div style="padding-top:0.75rem;margin-top:0.5rem;border-top:1px dashed #ccfbf1;">
                <button type="submit"
                        style="background:#0d9488;color:white;padding:0.625rem 1.5rem;border:none;border-radius:0.5rem;font-weight:600;font-size:0.9rem;cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;box-shadow:0 1px 3px rgba(13,148,136,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Enregistrer les capacités
                </button>
                <span style="font-size:0.75rem;color:#6b7280;margin-left:0.75rem;">
                    ↑ bouton spécifique à cette section
                </span>
            </div>
        @endif
    </form>
</div>
