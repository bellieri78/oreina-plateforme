<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div>
        <div class="form-group">
            <label class="form-label" for="title">Titre *</label>
            <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $event->title ?? '') }}" required>
            @error('title')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="slug">Slug (URL)</label>
            <input type="text" name="slug" id="slug" class="form-input" value="{{ old('slug', $event->slug ?? '') }}" placeholder="Genere automatiquement si vide">
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description courte</label>
            <textarea name="description" id="description" class="form-input" rows="3">{{ old('description', $event->description ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="content">Contenu détaillé</label>
            <input type="hidden" name="content" id="content-input" value="{{ old('content', $event->content ?? '') }}">
            <div id="editor" style="min-height: 300px; background: white; border: 1px solid #d1d5db; border-radius: 8px;"></div>
            @error('content')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-container { font-family: 'Inter', sans-serif; font-size: 15px; line-height: 1.7; }
    .ql-editor { min-height: 280px; color: #1C2B27; }
    .ql-editor h2 { font-size: 24px; font-weight: 700; margin: 1.2em 0 0.5em; }
    .ql-editor h3 { font-size: 20px; font-weight: 700; margin: 1em 0 0.4em; }
    .ql-editor blockquote { border-left: 3px solid #85B79D; padding-left: 16px; color: #68756F; }
    .ql-editor a { color: #356B8A; }
    .ql-toolbar { border-radius: 8px 8px 0 0; border-color: #d1d5db; background: #fafafa; }
    .ql-container { border-radius: 0 0 8px 8px; border-color: #d1d5db; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Rédigez le contenu détaillé de l\'événement...',
            modules: {
                toolbar: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['blockquote', 'link'],
                    [{ align: [] }],
                    ['clean']
                ]
            }
        });
        const existingContent = document.getElementById('content-input').value;
        if (existingContent) {
            quill.root.innerHTML = existingContent;
        }
        quill.root.closest('form').addEventListener('submit', function() {
            document.getElementById('content-input').value = quill.root.innerHTML;
        });
    });
</script>
@endpush

        <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">Lieu</h4>

        <div class="form-group">
            <label class="form-label" for="location_name">Nom du lieu</label>
            <input type="text" name="location_name" id="location_name" class="form-input" value="{{ old('location_name', $event->location_name ?? '') }}" placeholder="ex: Salle des fetes">
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="location_address">Adresse</label>
                <input type="text" name="location_address" id="location_address" class="form-input" value="{{ old('location_address', $event->location_address ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="location_city">Ville</label>
                <input type="text" name="location_city" id="location_city" class="form-input" value="{{ old('location_city', $event->location_city ?? '') }}">
            </div>
        </div>
    </div>

    <div>
        <div class="form-group">
            <label class="form-label" for="status">Statut *</label>
            <select name="status" id="status" class="form-input" required>
                <option value="draft" {{ old('status', $event->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                <option value="published" {{ old('status', $event->status ?? '') === 'published' ? 'selected' : '' }}>Publie</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="event_type">Type</label>
            <input type="text" name="event_type" id="event_type" class="form-input" value="{{ old('event_type', $event->event_type ?? '') }}" placeholder="ex: Conference, Sortie terrain...">
        </div>

        <div class="form-group">
            <label class="form-label" for="start_date">Date de debut *</label>
            <input type="datetime-local" name="start_date" id="start_date" class="form-input" value="{{ old('start_date', isset($event) && $event->start_date ? $event->start_date->format('Y-m-d\TH:i') : '') }}" required>
            @error('start_date')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="end_date">Date de fin</label>
            <input type="datetime-local" name="end_date" id="end_date" class="form-input" value="{{ old('end_date', isset($event) && $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : '') }}">
        </div>

        <div class="form-group">
            <label class="form-label" for="organizer_id">Organisateur</label>
            <select name="organizer_id" id="organizer_id" class="form-input">
                <option value="">-- Utilisateur courant --</option>
                @foreach($organizers as $organizer)
                    <option value="{{ $organizer->id }}" {{ old('organizer_id', $event->organizer_id ?? '') == $organizer->id ? 'selected' : '' }}>
                        {{ $organizer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="featured_image">Image de couverture</label>
            @if(isset($event) && $event->featured_image)
                <div style="margin-bottom: 0.5rem;">
                    <img src="{{ Storage::url($event->featured_image) }}" alt="Image actuelle" style="max-height: 120px; border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
            @endif
            <input type="file" name="featured_image" id="featured_image" class="form-input" accept="image/*">
            <p style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">JPG, PNG ou WebP. Max 5 Mo.</p>
            @error('featured_image')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">Inscription</h4>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="registration_required" value="1" {{ old('registration_required', $event->registration_required ?? false) ? 'checked' : '' }} style="width: auto;">
                <span>Inscription obligatoire</span>
            </label>
        </div>

        <div class="form-group">
            <label class="form-label" for="registration_url">URL d'inscription</label>
            <input type="url" name="registration_url" id="registration_url" class="form-input" value="{{ old('registration_url', $event->registration_url ?? '') }}" placeholder="https://...">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="max_participants">Participants max</label>
                <input type="number" name="max_participants" id="max_participants" class="form-input" value="{{ old('max_participants', $event->max_participants ?? '') }}" min="1">
            </div>
            <div class="form-group">
                <label class="form-label" for="price">Prix (EUR)</label>
                <input type="number" step="0.01" name="price" id="price" class="form-input" value="{{ old('price', $event->price ?? '') }}" min="0">
            </div>
        </div>
    </div>
</div>
