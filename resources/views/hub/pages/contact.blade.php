@extends('layouts.hub')

@section('title', 'Contact')
@section('meta_description', 'Contactez OREINA pour toute question sur l\'association, les adhésions ou les Lépidoptères de France.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-16 bg-warm">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="eyebrow blue mb-6">
                <i class="icon icon-blue" data-lucide="mail"></i>
                Nous écrire
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Contact</h1>
            <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl mx-auto">
                Une question ? Nous sommes là pour vous aider
            </p>
        </div>
    </section>

    {{-- Contact Content --}}
    <section class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Contact Info --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Email Card --}}
                    <div class="card p-6">
                        <div class="flex items-start gap-4">
                            <div class="pub-card-icon sage flex-shrink-0">
                                <i class="icon icon-sage" data-lucide="mail"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-oreina-dark mb-1">Email</h3>
                                <a href="mailto:contact@oreina.org" class="text-oreina-green hover:underline">
                                    contact@oreina.org
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Address Card --}}
                    <div class="card p-6">
                        <div class="flex items-start gap-4">
                            <div class="pub-card-icon coral flex-shrink-0">
                                <i class="icon icon-coral" data-lucide="map-pin"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-oreina-dark mb-1">Adresse</h3>
                                <p class="text-slate-600">
                                    Association OREINA<br>
                                    France
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Response Time Card --}}
                    <div class="card p-6">
                        <div class="flex items-start gap-4">
                            <div class="pub-card-icon gold flex-shrink-0">
                                <i class="icon icon-gold" data-lucide="clock"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-oreina-dark mb-1">Délai de réponse</h3>
                                <p class="text-slate-600">
                                    Nous répondons généralement sous 48h.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Social Links --}}
                    <div class="card p-6">
                        <h3 class="font-bold text-oreina-dark mb-4">Suivez-nous</h3>
                        <div class="flex gap-3">
                            <a href="#" class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-oreina-green hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33C10.24.5,9.5,3.44,9.5,5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4Z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-oreina-green hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-oreina-green hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Contact Form --}}
                <div class="lg:col-span-2">
                    <div class="card p-8 lg:p-10">
                        <h2 class="text-2xl font-bold text-oreina-dark mb-2">Envoyez-nous un message</h2>
                        <p class="text-slate-500 mb-8">Remplissez le formulaire ci-dessous et nous vous répondrons dans les meilleurs délais.</p>

                        <form action="#" method="POST" class="space-y-6">
                            @csrf

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-bold text-oreina-dark mb-2">Nom complet</label>
                                    <input type="text" id="name" name="name" required
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-oreina-beige/30 rounded-xl focus:ring-2 focus:ring-oreina-green focus:border-oreina-green transition"
                                           placeholder="Jean Dupont">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-bold text-oreina-dark mb-2">Email</label>
                                    <input type="email" id="email" name="email" required
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-oreina-beige/30 rounded-xl focus:ring-2 focus:ring-oreina-green focus:border-oreina-green transition"
                                           placeholder="jean@exemple.fr">
                                </div>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-bold text-oreina-dark mb-2">Sujet</label>
                                <select id="subject" name="subject" required
                                        class="w-full px-4 py-3 bg-slate-50 border-2 border-oreina-beige/30 rounded-xl focus:ring-2 focus:ring-oreina-green focus:border-oreina-green transition">
                                    <option value="">Sélectionnez un sujet</option>
                                    <option value="adhesion">Question sur l'adhésion</option>
                                    <option value="revue">Question sur la revue</option>
                                    <option value="evenement">Question sur un événement</option>
                                    <option value="identification">Aide à l'identification</option>
                                    <option value="partenariat">Proposition de partenariat</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-bold text-oreina-dark mb-2">Message</label>
                                <textarea id="message" name="message" rows="6" required
                                          class="w-full px-4 py-3 bg-slate-50 border-2 border-oreina-beige/30 rounded-xl focus:ring-2 focus:ring-oreina-green focus:border-oreina-green transition resize-none"
                                          placeholder="Décrivez votre demande..."></textarea>
                            </div>

                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="privacy" name="privacy" required
                                       class="mt-1 w-5 h-5 text-oreina-green border-2 border-oreina-beige/50 rounded focus:ring-oreina-green">
                                <label for="privacy" class="text-sm text-slate-600">
                                    J'accepte que mes données soient utilisées pour répondre à ma demande, conformément à notre <a href="#" class="text-oreina-green font-semibold hover:underline">politique de confidentialité</a>.
                                </label>
                            </div>

                            <button type="submit" class="btn-primary py-4 px-8">
                                <i data-lucide="send" style="width:20px;height:20px"></i>
                                Envoyer le message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ Preview --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-8 lg:p-12 text-center bg-gradient-to-br from-oreina-beige/30 to-slate-50">
                <div class="pub-card-icon sage mx-auto mb-6">
                    <i class="icon icon-sage" data-lucide="help-circle"></i>
                </div>
                <h2 class="text-2xl font-bold text-oreina-dark mb-4">Consultez notre FAQ</h2>
                <p class="text-slate-600 mb-8 max-w-xl mx-auto">
                    Vous trouverez peut-être la réponse à votre question dans notre section adhésion.
                </p>
                <a href="{{ route('hub.membership') }}#faq" class="btn-secondary">
                    Voir les questions fréquentes
                </a>
            </div>
        </div>
    </section>
@endsection
