<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div>
        <div class="form-group">
            <label for="title" class="form-label">Titre *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $activity->title ?? '') }}"
                   class="form-input @error('title') is-invalid @enderror" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="activity_type_id" class="form-label">Type d'activite *</label>
            <select name="activity_type_id" id="activity_type_id" class="form-input @error('activity_type_id') is-invalid @enderror" required>
                <option value="">-- Selectionner --</option>
                @foreach($activityTypes as $type)
                    <option value="{{ $type->id }}" {{ old('activity_type_id', $activity->activity_type_id ?? '') == $type->id ? 'selected' : '' }}
                            data-color="{{ $type->color }}">
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            @error('activity_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="structure_id" class="form-label">Structure</label>
            <select name="structure_id" id="structure_id" class="form-input">
                <option value="">-- Aucune --</option>
                @foreach($structures as $id => $name)
                    <option value="{{ $id }}" {{ old('structure_id', $activity->structure_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="form-input">{{ old('description', $activity->description ?? '') }}</textarea>
        </div>

        @isset($activity)
            <div class="form-group">
                <label for="status" class="form-label">Statut *</label>
                <select name="status" id="status" class="form-input" required>
                    @foreach(\App\Models\VolunteerActivity::getStatuses() as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $activity->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endisset
    </div>

    <div>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="activity_date" class="form-label">Date *</label>
                <input type="date" name="activity_date" id="activity_date"
                       value="{{ old('activity_date', isset($activity) ? $activity->activity_date->format('Y-m-d') : '') }}"
                       class="form-input @error('activity_date') is-invalid @enderror" required>
                @error('activity_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="start_time" class="form-label">Debut</label>
                <input type="time" name="start_time" id="start_time"
                       value="{{ old('start_time', isset($activity) && $activity->start_time ? \Carbon\Carbon::parse($activity->start_time)->format('H:i') : '') }}"
                       class="form-input">
            </div>
            <div class="form-group">
                <label for="end_time" class="form-label">Fin</label>
                <input type="time" name="end_time" id="end_time"
                       value="{{ old('end_time', isset($activity) && $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : '') }}"
                       class="form-input">
            </div>
        </div>

        <div class="form-group">
            <label for="location" class="form-label">Lieu</label>
            <input type="text" name="location" id="location" value="{{ old('location', $activity->location ?? '') }}" class="form-input">
        </div>

        <div class="form-group">
            <label for="city" class="form-label">Ville</label>
            <input type="text" name="city" id="city" value="{{ old('city', $activity->city ?? '') }}" class="form-input">
        </div>

        <div class="form-group">
            <label for="organizer_id" class="form-label">Organisateur</label>
            <select name="organizer_id" id="organizer_id" class="form-input">
                <option value="">-- Selectionner --</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ old('organizer_id', $activity->organizer_id ?? '') == $member->id ? 'selected' : '' }}>
                        {{ $member->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="max_participants" class="form-label">Nombre max de participants</label>
            <input type="number" name="max_participants" id="max_participants" min="1"
                   value="{{ old('max_participants', $activity->max_participants ?? '') }}" class="form-input">
        </div>

        <div class="form-group">
            <label for="notes" class="form-label">Notes internes</label>
            <textarea name="notes" id="notes" rows="2" class="form-input">{{ old('notes', $activity->notes ?? '') }}</textarea>
        </div>
    </div>
</div>
