@extends('layouts.hub')

@section('title', 'Contact')
@section('meta_description', 'Contactez OREINA pour toute question sur l\'association, les adhésions ou les Lépidoptères de France.')

@section('content')
    {{-- Header --}}
    <section class="pt-28 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="icon-box bg-oreina-blue text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="20" height="16" x="2" y="4" rx="2"/>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-oreina-dark">Contact</h1>
                    <p class="text-slate-500 mt-1">Une question ? Nous sommes là pour vous aider</p>
                </div>
            </div>
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
                            <div class="w-12 h-12 bg-gradient-to-br from-oreina-green to-oreina-teal rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
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
                            <div class="w-12 h-12 bg-gradient-to-br from-oreina-turquoise to-oreina-blue rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
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
                            <div class="w-12 h-12 bg-gradient-to-br from-oreina-yellow to-oreina-coral rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
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
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="m22 2-7 20-4-9-9-4z"/>
                                    <path d="M22 2 11 13"/>
                                </svg>
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
                <div class="w-16 h-16 bg-oreina-green/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-oreina-green" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <path d="M12 17h.01"/>
                    </svg>
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
