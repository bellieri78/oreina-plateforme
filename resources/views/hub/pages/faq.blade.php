@extends('layouts.hub')

@section('title', 'Foire aux questions')
@section('meta_description', 'Toutes les questions fréquentes sur oreina, sur les papillons de France et sur la pratique de la lépidoptérologie : adhésion, identification, données, éthique, conservation.')

@push('scripts')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
@endverbatim
@php
    $allVisible = collect();
    foreach ($sections as $s) {
        $allVisible = $allVisible->concat($questionsBySection[$s['slug']] ?? collect());
    }
@endphp
@foreach($allVisible as $i => $q)
    {"@type":"Question","name":{!! json_encode(strip_tags($q->question), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!},"acceptedAnswer":{"@type":"Answer","text":{!! json_encode(strip_tags($q->answer), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}}}@if(! $loop->last),@endif
@endforeach
@verbatim
  ]
}
</script>
@endverbatim
@endpush

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-16 bg-warm">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="eyebrow gold mb-6">
                <i class="icon icon-gold" data-lucide="help-circle"></i>
                FAQ
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Foire aux questions</h1>
            <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl mx-auto">
                Tout ce que vous vouliez savoir sur oreina, sur les papillons, et sur la pratique de la lépidoptérologie
            </p>
        </div>
    </section>

    {{-- Sommaire / navigation rapide --}}
    <section class="py-10 bg-slate-50 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-sm text-slate-500 mb-4 text-center">Naviguer dans la FAQ</p>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach($sections as $s)
                    <a href="#{{ $s['slug'] }}" class="px-4 py-2 bg-white rounded-full text-sm font-bold text-oreina-dark hover:bg-oreina-green/10 transition border border-slate-200">{{ \Illuminate\Support\Str::after($s['label'], ' ') }}</a>
                @endforeach
            </div>
        </div>
    </section>

    @foreach($sections as $i => $section)
        @php
            $questions = $questionsBySection[$section['slug']] ?? collect();
            $bg = $i % 2 === 0 ? 'bg-white' : 'bg-slate-50';
        @endphp
        <section id="{{ $section['slug'] }}" class="py-16 {{ $bg }} scroll-mt-24">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-10">
                    <div class="eyebrow {{ $section['color'] }} mb-4 inline-flex">
                        <i class="icon icon-{{ $section['color'] }}" data-lucide="{{ $section['icon'] }}"></i>
                        {!! $section['label'] !!}
                    </div>
                    <h2 class="text-3xl font-bold text-oreina-dark">{{ $section['title'] }}</h2>
                </div>

                <div class="space-y-3">
                    @forelse($questions as $q)
                        <details class="card p-0 group">
                            <summary class="cursor-pointer p-5 font-bold text-oreina-dark flex items-center justify-between hover:bg-slate-50 transition">
                                <span>{!! $q->question !!}</span>
                                <i data-lucide="chevron-down" class="text-slate-400 transition-transform group-open:rotate-180" style="width:20px;height:20px"></i>
                            </summary>
                            <div class="px-5 pb-5 text-slate-600 prose">
                                {!! $q->answer !!}
                            </div>
                        </details>
                    @empty
                        <p class="text-slate-500 text-sm italic">Aucune question dans cette section pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endforeach

    {{-- CTA final --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="message-circle"></i>Une question ?</div>
                <h2>Vous ne trouvez pas votre réponse ?</h2>
                <p>N'hésitez pas à nous écrire. Pour les questions scientifiques pointues, vous pouvez aussi solliciter directement l'un des groupes de travail thématiques.</p>
                <div class="content-actions">
                    <a href="{{ route('hub.contact') }}" class="btn btn-primary">
                        <i class="icon icon-sage" data-lucide="mail"></i>
                        Nous contacter
                    </a>
                    <a href="{{ route('hub.membership') }}" class="btn btn-ghost-light">
                        <i class="icon icon-white" data-lucide="heart-plus"></i>
                        Devenir membre
                    </a>
                </div>
            </article>
        </div>
    </section>
@endsection
