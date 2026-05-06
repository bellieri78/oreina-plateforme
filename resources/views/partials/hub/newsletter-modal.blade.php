{{-- Modale d'inscription Newsletter (Alpine.js)
     S'attend à un parent avec x-data exposant `newsletterOpen` (booléen). --}}
<div class="newsletter-modal"
     x-show="newsletterOpen"
     x-transition.opacity
     x-cloak
     @keydown.escape.window="newsletterOpen = false"
     x-data="{
        loading: false,
        success: false,
        errors: {},
        form: { email: '', first_name: '', last_name: '', consent: false },
        async submit() {
            this.loading = true;
            this.errors = {};
            try {
                const res = await fetch('{{ route('hub.newsletter.subscribe') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (res.status === 422) {
                    this.errors = data.errors || {};
                } else if (data.success) {
                    this.success = true;
                    setTimeout(() => {
                        this.newsletterOpen = false;
                        this.success = false;
                        this.form = { email: '', first_name: '', last_name: '', consent: false };
                    }, 2500);
                } else {
                    this.errors = { _global: [data.message || 'Une erreur est survenue.'] };
                }
            } catch (e) {
                this.errors = { _global: ['Erreur réseau. Réessayez.'] };
            } finally {
                this.loading = false;
            }
        }
     }">
    <div class="newsletter-modal-overlay" @click="newsletterOpen = false"></div>

    <div class="newsletter-modal-card" role="dialog" aria-modal="true" aria-labelledby="newsletter-modal-title">
        <button type="button"
                class="newsletter-modal-close"
                @click="newsletterOpen = false"
                aria-label="Fermer">
            <i data-lucide="x"></i>
        </button>

        <div x-show="!success">
            <h3 id="newsletter-modal-title" class="newsletter-modal-title">
                Recevez notre newsletter
            </h3>
            <p class="newsletter-modal-lede">
                Actualités, nouveaux numéros de la revue et événements OREINA dans votre boîte mail.
            </p>

            <form @submit.prevent="submit()" class="newsletter-form">
                <div class="newsletter-form-row">
                    <label class="newsletter-form-field">
                        <span>Prénom <em>(facultatif)</em></span>
                        <input type="text" x-model="form.first_name" maxlength="100">
                    </label>
                    <label class="newsletter-form-field">
                        <span>Nom <em>(facultatif)</em></span>
                        <input type="text" x-model="form.last_name" maxlength="100">
                    </label>
                </div>

                <label class="newsletter-form-field">
                    <span>Email <em>*</em></span>
                    <input type="email" x-model="form.email" required maxlength="255">
                    <template x-if="errors.email">
                        <small class="newsletter-form-error" x-text="errors.email[0]"></small>
                    </template>
                </label>

                <label class="newsletter-form-checkbox">
                    <input type="checkbox" x-model="form.consent">
                    <span>
                        J'accepte de recevoir les informations d'OREINA par email. Je peux me désinscrire
                        à tout moment via le lien dans chaque message.
                    </span>
                </label>
                <template x-if="errors.consent">
                    <small class="newsletter-form-error" x-text="errors.consent[0]"></small>
                </template>

                <template x-if="errors._global">
                    <p class="newsletter-form-error newsletter-form-error-global" x-text="errors._global[0]"></p>
                </template>

                <button type="submit" class="btn btn-primary newsletter-form-submit" :disabled="loading">
                    <span x-show="!loading">Je m'inscris</span>
                    <span x-show="loading" x-cloak>Envoi…</span>
                </button>
            </form>
        </div>

        <div x-show="success" x-cloak class="newsletter-success">
            <i data-lucide="check-circle-2" class="newsletter-success-icon"></i>
            <p>Merci ! Vous êtes inscrit·e à la newsletter.</p>
        </div>
    </div>
</div>
