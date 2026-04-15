@auth
    @unless(auth()->user()->hasVerifiedEmail())
        <div class="bg-amber-50 border-b border-amber-200 px-4 py-2 text-sm text-amber-900">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                <span>
                    <strong>Votre adresse email n'est pas encore vérifiée.</strong>
                    Certaines fonctionnalités (notamment la soumission d'articles) restent inaccessibles tant que c'est pas fait.
                </span>
                <form method="POST" action="{{ route('verification.send') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="underline font-medium hover:text-amber-700">
                        Renvoyer le mail
                    </button>
                </form>
            </div>
        </div>
    @endunless
@endauth
