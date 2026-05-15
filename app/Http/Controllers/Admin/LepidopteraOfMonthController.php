<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LepidopteraOfMonth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LepidopteraOfMonthController extends Controller
{
    public function index()
    {
        $entries = LepidopteraOfMonth::ordered()->paginate(20);
        return view('admin.espece-du-mois.index', compact('entries'));
    }

    public function create()
    {
        return view('admin.espece-du-mois.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $data['photo_path'] = $this->handlePhotoUpload($request);

        LepidopteraOfMonth::create($data);

        return redirect()->route('admin.espece-du-mois.index')
            ->with('success', 'Espèce ajoutée au carousel.');
    }

    public function edit(LepidopteraOfMonth $espece_du_mois)
    {
        return view('admin.espece-du-mois.edit', ['entry' => $espece_du_mois]);
    }

    public function update(Request $request, LepidopteraOfMonth $espece_du_mois)
    {
        $data = $this->validatePayload($request);

        if ($request->hasFile('photo')) {
            if ($espece_du_mois->photo_path && str_starts_with($espece_du_mois->photo_path, 'espece-du-mois/')) {
                Storage::disk('public')->delete($espece_du_mois->photo_path);
            }
            $data['photo_path'] = $this->handlePhotoUpload($request);
        }

        $espece_du_mois->update($data);

        return redirect()->route('admin.espece-du-mois.index')
            ->with('success', 'Espèce mise à jour.');
    }

    public function destroy(LepidopteraOfMonth $espece_du_mois)
    {
        if ($espece_du_mois->photo_path && str_starts_with($espece_du_mois->photo_path, 'espece-du-mois/')) {
            Storage::disk('public')->delete($espece_du_mois->photo_path);
        }
        $espece_du_mois->delete();

        return redirect()->route('admin.espece-du-mois.index')
            ->with('success', 'Espèce supprimée.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'scientific_name' => 'required|string|max:255',
            'photographer' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'photo' => 'nullable|image|max:8192',
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }

    private function handlePhotoUpload(Request $request): string
    {
        if (!$request->hasFile('photo')) {
            return '';
        }
        return $request->file('photo')->store('espece-du-mois', 'public');
    }
}
