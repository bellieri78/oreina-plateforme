@extends('layouts.hub')

@section('title', 'Adhésion')
@section('meta_description', 'Rejoignez OREINA et soutenez l\'étude et la protection des Lépidoptères de France. Découvrez nos formules d\'adhésion.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-16 bg-warm">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="eyebrow gold mb-6">
                <i class="icon icon-gold" data-lucide="heart-handshake"></i>
                Adhésion
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Rejoignez OREINA</h1>
            <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl mx-auto">
                Devenez membre et participez à la connaissance des Lépidoptères
            </p>
        </div>
    </section>

    {{-- Benefits --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-green/10 text-oreina-green text-sm font-bold">Avantages</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Les avantages de l'adhésion</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-green to-oreina-teal rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="book-open" style="width:32px;height:32px;color:white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-oreina-dark mb-3">Revue scientifique</h3>
                    <p class="text-slate-600">Recevez la revue OREINA, publication de référence sur les Lépidoptères de France (4 numéros par an).</p>
                </div>

                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-turquoise to-oreina-blue rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="binoculars" style="width:32px;height:32px;color:white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-oreina-dark mb-3">Sorties terrain</h3>
                    <p class="text-slate-600">Participez aux sorties d'observation organisées par l'association dans toute la France.</p>
                </div>

                <div class="card p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-oreina-yellow to-oreina-coral rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="badge-check" style="width:32px;height:32px;color:white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-oreina-dark mb-3">Réseau d'experts</h3>
                    <p class="text-slate-600">Échangez avec des passionnés et des spécialistes des papillons de toute la France.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-turquoise/10 text-oreina-turquoise text-sm font-bold">Tarifs</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Nos formules d'adhésion</h2>
                <p class="text-slate-600 mt-2 max-w-2xl mx-auto">
                    Choisissez la formule qui vous convient. Toutes les adhésions sont valables un an à compter de la date de souscription.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
                @foreach($membershipTypes as $type)
                <div class="card {{ $type->slug === 'individuelle' ? 'ring-2 ring-oreina-green' : '' }} overflow-hidden">
                    @if($type->slug === 'individuelle')
                    <div class="bg-gradient-to-r from-oreina-green to-oreina-teal text-white text-center text-sm font-bold py-2">
                        Le plus populaire
                    </div>
                    @endif
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-oreina-dark mb-2">{{ $type->name }}</h3>
                        <p class="text-slate-600 text-sm mb-6">{{ $type->description }}</p>

                        <div class="mb-8">
                            <span class="text-5xl font-bold text-oreina-dark">{{ number_format($type->price, 0, ',', ' ') }}</span>
                            <span class="text-slate-500 text-lg">€ / an</span>
                        </div>

                        @if($type->features)
                        <ul class="space-y-4 mb-8">
                            @foreach($type->features as $feature)
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-oreina-green/10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="check" style="width:12px;height:12px;color:var(--color-oreina-green)"></i>
                                </div>
                                <span class="text-slate-600 text-sm">{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        <a href="https://www.helloasso.com/associations/oreina" target="_blank" rel="noopener" class="{{ $type->slug === 'individuelle' ? 'btn-primary' : 'btn-secondary' }} w-full justify-center">
                            Adhérer maintenant
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-center gap-2 mt-8 text-sm text-slate-500">
                <i data-lucide="lock" style="width:16px;height:16px"></i>
                Paiement sécurisé via HelloAsso
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="py-16 bg-slate-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="px-4 py-2 rounded-full bg-oreina-yellow/20 text-oreina-dark text-sm font-bold">FAQ</span>
                <h2 class="text-2xl font-bold text-oreina-dark mt-4">Questions fréquentes</h2>
            </div>

            <div class="space-y-4">
                <details class="card p-6 group">
                    <summary class="flex justify-between items-center cursor-pointer font-bold text-oreina-dark">
                        Comment adhérer à OREINA ?
                        <div class="w-8 h-8 bg-oreina-beige/50 rounded-full flex items-center justify-center group-open:bg-oreina-green group-open:text-white transition">
                            <i data-lucide="chevron-down" class="group-open:rotate-180 transition" style="width:16px;height:16px"></i>
                        </div>
                    </summary>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        L'adhésion se fait en ligne via notre partenaire HelloAsso. Choisissez votre formule, remplissez le formulaire et procédez au paiement sécurisé. Vous recevrez une confirmation par email.
                    </p>
                </details>

                <details class="card p-6 group">
                    <summary class="flex justify-between items-center cursor-pointer font-bold text-oreina-dark">
                        Quand commence mon adhésion ?
                        <div class="w-8 h-8 bg-oreina-beige/50 rounded-full flex items-center justify-center group-open:bg-oreina-green group-open:text-white transition">
                            <i data-lucide="chevron-down" class="group-open:rotate-180 transition" style="width:16px;height:16px"></i>
                        </div>
                    </summary>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        Votre adhésion est valable 12 mois à compter de la date de paiement. Vous recevrez la revue OREINA dès le prochain numéro publié.
                    </p>
                </details>

                <details class="card p-6 group">
                    <summary class="flex justify-between items-center cursor-pointer font-bold text-oreina-dark">
                        Mon adhésion est-elle déductible des impôts ?
                        <div class="w-8 h-8 bg-oreina-beige/50 rounded-full flex items-center justify-center group-open:bg-oreina-green group-open:text-white transition">
                            <i data-lucide="chevron-down" class="group-open:rotate-180 transition" style="width:16px;height:16px"></i>
                        </div>
                    </summary>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        OREINA est une association loi 1901 d'intérêt général. Les dons sont déductibles à 66% dans la limite de 20% du revenu imposable. Un reçu fiscal vous sera envoyé.
                    </p>
                </details>

                <details class="card p-6 group">
                    <summary class="flex justify-between items-center cursor-pointer font-bold text-oreina-dark">
                        Puis-je faire un don sans adhérer ?
                        <div class="w-8 h-8 bg-oreina-beige/50 rounded-full flex items-center justify-center group-open:bg-oreina-green group-open:text-white transition">
                            <i data-lucide="chevron-down" class="group-open:rotate-180 transition" style="width:16px;height:16px"></i>
                        </div>
                    </summary>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        Oui, vous pouvez soutenir OREINA par un don libre sans adhérer. Les dons permettent de financer nos actions de conservation et nos publications.
                    </p>
                </details>
            </div>
        </div>
    </section>

    {{-- Contact CTA --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="stats-banner text-center">
                <h2 class="text-2xl font-bold mb-4">Une question sur l'adhésion ?</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">
                    N'hésitez pas à nous contacter pour toute question concernant les formules d'adhésion ou la revue OREINA.
                </p>
                <a href="{{ route('hub.contact') }}" class="btn btn-primary">
                    <i class="icon icon-sage" data-lucide="mail"></i>
                    Nous contacter
                </a>
            </div>
        </div>
    </section>
@endsection
