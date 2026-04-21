@php
    $user = auth()->user();
    $member = $user?->member;
    $isCurrentMember = (bool) ($member?->isCurrentMember() ?? false);
    $canDownload = $bulletin->isPublic() || ($bulletin->isInMembersPhase() && $isCurrentMember);
@endphp

<article class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
    <a href="{{ route('hub.lepis.bulletins.show', $bulletin) }}" class="block">
        @if ($bulletin->cover_image)
            <img src="{{ Storage::url($bulletin->cover_image) }}" alt="Couverture Lepis n°{{ $bulletin->issue_number }}" class="w-full h-48 object-cover">
        @else
            <div class="w-full h-48 bg-oreina-green text-white flex items-center justify-center text-4xl font-bold">
                n°{{ $bulletin->issue_number }}
            </div>
        @endif
    </a>

    <div class="p-5">
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
            <span>Lepis n°{{ $bulletin->issue_number }}</span>
            <span>•</span>
            <span>{{ $bulletin->quarter_label }} {{ $bulletin->year }}</span>
            @if ($bulletin->isInMembersPhase())
                <span class="ml-auto px-2 py-0.5 bg-amber-100 text-amber-800 rounded">Réservé adhérents</span>
            @endif
        </div>

        <h2 class="text-lg font-semibold text-oreina-dark mb-2">
            <a href="{{ route('hub.lepis.bulletins.show', $bulletin) }}" class="hover:text-oreina-green">{{ $bulletin->title }}</a>
        </h2>

        @if ($bulletin->summary)
            <p class="text-sm text-gray-600 mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($bulletin->summary), 140) }}</p>
        @endif

        @if ($canDownload)
            <a href="{{ route('hub.lepis.bulletins.download', $bulletin) }}"
               class="inline-flex items-center px-3 py-1.5 bg-oreina-green text-white text-sm rounded hover:bg-oreina-dark">
                Télécharger le PDF
            </a>
        @elseif (! $user)
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('hub.login') }}" class="px-3 py-1.5 bg-oreina-green text-white text-sm rounded hover:bg-oreina-dark">Se connecter</a>
                <a href="{{ route('hub.membership') }}" class="px-3 py-1.5 bg-oreina-beige text-oreina-dark text-sm rounded hover:bg-oreina-light">Adhérer</a>
            </div>
        @else
            <a href="{{ route('hub.membership') }}" class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white text-sm rounded hover:bg-amber-600">
                Renouveler ma cotisation
            </a>
        @endif
    </div>
</article>
