<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Question</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="section">Section *</label>
            <select name="section" id="section" class="form-input" required>
                @foreach($sections as $s)
                    <option value="{{ $s['slug'] }}" {{ old('section', $faq?->section ?? ($defaultSection ?? '')) === $s['slug'] ? 'selected' : '' }}>
                        {{ $s['label'] }}
                    </option>
                @endforeach
            </select>
            @error('section')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="question">Question *</label>
            <input type="text" name="question" id="question" class="form-input" required maxlength="500"
                   value="{{ old('question', $faq?->question ?? '') }}"
                   placeholder="Ex. Comment devenir adhérent ?">
            <small style="color: #6b7280; font-size: 0.8125rem;">Tu peux utiliser <code>&lt;em&gt;</code> pour mettre un nom de revue en italique.</small>
            @error('question')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="editor">Réponse *</label>
            <input type="hidden" name="answer" id="answer-input" value="{{ old('answer', $faq?->answer ?? '') }}">
            <div id="editor" style="background: white;"></div>
            @error('answer')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
        <style>
            .ql-container { font-family: 'Inter', sans-serif; font-size: 15px; line-height: 1.7; }
            .ql-editor { min-height: 240px; color: #1C2B27; }
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
                    placeholder: 'Rédigez la réponse à la question...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['link'],
                            ['clean']
                        ]
                    }
                });

                const existing = document.getElementById('answer-input').value;
                if (existing) quill.root.innerHTML = existing;

                quill.root.closest('form').addEventListener('submit', function() {
                    document.getElementById('answer-input').value = quill.root.innerHTML;
                });
            });
        </script>
        @endpush
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Visibilité</h3></div>
    <div class="card-body">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
            <input type="checkbox" name="is_visible" value="1" {{ old('is_visible', $faq?->is_visible ?? true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
            <span>Question visible sur le site public</span>
        </label>
    </div>
</div>
