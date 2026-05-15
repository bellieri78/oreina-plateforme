@if($lepidopteraSlides->count() === 0)
    <div class="welcome-photo">
        <img src="{{ asset('images/espace-membre/papillon-hero.jpg') }}" alt="Papillon — espace OREINA" loading="eager">
    </div>
@else
    <div class="hero-carousel" x-data="{
        slides: {{ $lepidopteraSlides->count() }},
        current: 0,
        timer: null,
        start() {
            this.timer = setInterval(() => this.next(), 6000);
        },
        stop() {
            if (this.timer) clearInterval(this.timer);
        },
        next() { this.current = (this.current + 1) % this.slides; },
        go(i) { this.current = i; this.stop(); this.start(); }
    }" x-init="start()" @mouseenter="stop()" @mouseleave="start()">

        @foreach($lepidopteraSlides as $i => $slide)
        <div class="hero-carousel-slide" x-show="current === {{ $i }}" x-transition.opacity.duration.500ms>
            <img src="{{ $slide->photoUrl() }}" alt="{{ $slide->scientific_name }}">
            <div class="hero-carousel-caption">
                <span class="eyebrow"><i data-lucide="leaf" style="width:12px;height:12px;"></i>Espèce du mois</span>
                <strong>{{ $slide->scientific_name }}</strong>
                @if($slide->photographer)
                    <small>Photo : {{ $slide->photographer }}</small>
                @endif
            </div>
        </div>
        @endforeach

        @if($lepidopteraSlides->count() > 1)
        <div class="hero-carousel-dots">
            @foreach($lepidopteraSlides as $i => $slide)
            <span class="hero-carousel-dot" :class="{ 'active': current === {{ $i }} }" @click="go({{ $i }})"></span>
            @endforeach
        </div>
        @endif
    </div>
@endif
