<style>
.file-upload-wrapper { margin-top: 0.25rem; }
.current-file {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 0.375rem;
    margin-top: 0.5rem;
    font-size: 0.875rem;
}
.current-file a {
    color: #15803d;
    text-decoration: none;
}
.current-file a:hover {
    text-decoration: underline;
}
.current-file svg {
    color: #15803d;
    flex-shrink: 0;
}
</style>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div>
        <div class="form-group">
            <label class="form-label" for="title">Titre *</label>
            <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $submission->title ?? '') }}" required>
            @error('title')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="abstract">Resume</label>
            <textarea name="abstract" id="abstract" class="form-input" rows="6">{{ old('abstract', $submission->abstract ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="keywords">Mots-cles</label>
            <input type="text" name="keywords" id="keywords" class="form-input" value="{{ old('keywords', $submission->keywords ?? '') }}" placeholder="Separes par des virgules">
        </div>

        @if(isset($submission))
            <div class="form-group">
                <label class="form-label" for="editor_notes">Notes editeur</label>
                <textarea name="editor_notes" id="editor_notes" class="form-input" rows="4">{{ old('editor_notes', $submission->editor_notes ?? '') }}</textarea>
            </div>
        @endif

        {{-- Link to Layout Page (for accepted/published articles) --}}
        @if(isset($submission) && in_array($submission->status?->value, ['accepted', 'published']))
            <div style="margin-top: 1.5rem; padding: 1.25rem; background: linear-gradient(135deg, #f0fdfa 0%, #f0f9ff 100%); border-radius: 0.75rem; border: 1px solid #99f6e4;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; background: #0d9488; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" style="color: white;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <div>
                            <h4 style="font-size: 0.95rem; font-weight: 600; color: #0d9488; margin: 0;">Mise en page de l'article</h4>
                            <p style="font-size: 0.8rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                                @php
                                    $blockCount = is_array($submission->content_blocks) ? count($submission->content_blocks) : 0;
                                @endphp
                                @if($blockCount > 0)
                                    {{ $blockCount }} bloc(s) de contenu
                                @else
                                    Aucun contenu saisi
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.submissions.layout', $submission) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #0d9488; color: white; border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        Maquetter
                    </a>
                </div>
            </div>
        @endif

        {{-- File Uploads --}}
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <h4 style="margin-bottom: 1rem; font-size: 0.875rem; color: #374151; font-weight: 600;">Fichiers</h4>

            <div class="form-group">
                <label class="form-label" for="manuscript_file">Manuscrit (Word, PDF)</label>
                <div class="file-upload-wrapper">
                    <input type="file" name="manuscript_file" id="manuscript_file" class="form-input" accept=".doc,.docx,.pdf,.odt">
                    @if(isset($submission) && $submission->manuscript_file)
                        <div class="current-file">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <a href="{{ route('admin.submissions.download', ['submission' => $submission, 'type' => 'manuscript']) }}" target="_blank">
                                {{ basename($submission->manuscript_file) }}
                            </a>
                            <label style="margin-left: auto; display: flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                                <input type="checkbox" name="remove_manuscript" value="1">
                                <span style="font-size: 0.75rem;">Supprimer</span>
                            </label>
                        </div>
                    @endif
                </div>
                @error('manuscript_file')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="pdf_file">PDF final (pour publication)</label>
                <div class="file-upload-wrapper">
                    <input type="file" name="pdf_file" id="pdf_file" class="form-input" accept=".pdf">
                    @if(isset($submission) && $submission->pdf_file)
                        <div class="current-file">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <a href="{{ route('admin.submissions.download', ['submission' => $submission, 'type' => 'pdf']) }}" target="_blank">
                                {{ basename($submission->pdf_file) }}
                            </a>
                            <label style="margin-left: auto; display: flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                                <input type="checkbox" name="remove_pdf" value="1">
                                <span style="font-size: 0.75rem;">Supprimer</span>
                            </label>
                        </div>
                    @endif
                </div>
                @error('pdf_file')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="featured_image">Image de couverture (pour les cartes articles)</label>
                <div class="file-upload-wrapper">
                    <input type="file" name="featured_image" id="featured_image" class="form-input" accept="image/*">
                    @if(isset($submission) && $submission->featured_image)
                        <div class="current-file" style="flex-direction: column; align-items: flex-start;">
                            <img src="{{ Storage::url($submission->featured_image) }}" alt="Image de couverture" style="max-width: 200px; max-height: 150px; border-radius: 0.375rem; margin-bottom: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                                <input type="checkbox" name="remove_featured_image" value="1">
                                <span style="font-size: 0.75rem;">Supprimer l'image</span>
                            </label>
                        </div>
                    @endif
                </div>
                @error('featured_image')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div>
        <div class="form-group">
            <label class="form-label" for="status">Statut *</label>
            <select name="status" id="status" class="form-input" required>
                @foreach(\App\Models\Submission::getStatuses() as $key => $label)
                    <option value="{{ $key }}" {{ old('status', $submission->status?->value ?? 'submitted') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" x-data="{ mode: '{{ old('author_mode', 'existing') }}' }">
            <label class="form-label">Auteur *</label>

            @if(!isset($submission))
                <div style="display: flex; gap: 1rem; margin-bottom: 0.75rem; font-size: 0.875rem;">
                    <label style="display: flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                        <input type="radio" name="author_mode" value="existing" x-model="mode"> Auteur existant
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                        <input type="radio" name="author_mode" value="new" x-model="mode"> Nouvel auteur
                    </label>
                </div>
            @else
                <input type="hidden" name="author_mode" value="existing">
            @endif

            <div x-show="mode === 'existing'">
                <select name="author_id" id="author_id" class="form-input" x-bind:required="mode === 'existing'">
                    <option value="">-- Selectionner --</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" {{ old('author_id', $submission->author_id ?? '') == $author->id ? 'selected' : '' }}>
                            {{ $author->name }}{{ $author->isGhost() ? ' (compte non activé)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('author_id')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div x-show="mode === 'new'" x-cloak>
                <input type="text" name="author_name" id="author_name"
                       class="form-input" placeholder="Nom complet"
                       value="{{ old('author_name') }}"
                       x-bind:required="mode === 'new'">
                @error('author_name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror

                <input type="email" name="author_email" id="author_email"
                       class="form-input" placeholder="Email"
                       value="{{ old('author_email') }}"
                       style="margin-top: 0.5rem;"
                       x-bind:required="mode === 'new'">
                @error('author_email')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror

                <p style="font-size: 0.8rem; color: #6b7280; margin-top: 0.5rem;">
                    Un compte sera créé pour l'auteur. Une invitation lui sera envoyée à cette adresse pour activer son accès.
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="journal_issue_id">Numero de publication</label>
            <select name="journal_issue_id" id="journal_issue_id" class="form-input">
                <option value="">-- Non assigne --</option>
                @foreach($issues as $issue)
                    <option value="{{ $issue->id }}" {{ old('journal_issue_id', $submission->journal_issue_id ?? $selectedIssue ?? '') == $issue->id ? 'selected' : '' }}>
                        Vol. {{ $issue->volume_number }} N°{{ $issue->issue_number }}
                        @if($issue->title) - {{ $issue->title }} @endif
                    </option>
                @endforeach
            </select>
        </div>

        @if(isset($editors))
            <div class="form-group">
                <label class="form-label" for="editor_id">Editeur responsable</label>
                <select name="editor_id" id="editor_id" class="form-input">
                    <option value="">-- Non assigne --</option>
                    @foreach($editors as $editor)
                        <option value="{{ $editor->id }}" {{ old('editor_id', $submission->editor_id ?? '') == $editor->id ? 'selected' : '' }}>
                            {{ $editor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="decision">Decision</label>
                <select name="decision" id="decision" class="form-input">
                    <option value="">-- Pas de decision --</option>
                    @foreach(\App\Models\Submission::getDecisions() as $key => $label)
                        <option value="{{ $key }}" {{ old('decision', $submission->decision ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">Publication</h4>

        <div class="form-group">
            <label class="form-label" for="doi">DOI</label>
            <input type="text" name="doi" id="doi" class="form-input" value="{{ old('doi', $submission->doi ?? '') }}">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="start_page">Page debut</label>
                <input type="number" name="start_page" id="start_page" class="form-input" value="{{ old('start_page', $submission->start_page ?? '') }}" min="1">
            </div>
            <div class="form-group">
                <label class="form-label" for="end_page">Page fin</label>
                <input type="number" name="end_page" id="end_page" class="form-input" value="{{ old('end_page', $submission->end_page ?? '') }}" min="1">
            </div>
        </div>
    </div>
</div>
