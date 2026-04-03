<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LepisBulletin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LepisBulletinController extends Controller
{
    public function index(Request $request)
    {
        $query = LepisBulletin::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('title', 'ilike', "%{$search}%");
        }

        $sortField = $request->get('sort', 'year');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['title', 'issue_number', 'year', 'created_at'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('year', 'desc')->orderBy('issue_number', 'desc');
        }

        $bulletins = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => LepisBulletin::count(),
            'published' => LepisBulletin::where('is_published', true)->count(),
        ];

        return view('admin.lepis.index', compact('bulletins', 'stats'));
    }

    public function create()
    {
        return view('admin.lepis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'issue_number' => 'required|integer|min:1',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'year' => 'required|integer|min:1900|max:2100',
            'pdf' => 'required|file|mimes:pdf|max:51200',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('pdf')) {
            $validated['pdf_path'] = $request->file('pdf')->store('lepis', 'public');
        }

        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        unset($validated['pdf']);

        $bulletin = LepisBulletin::create($validated);

        return redirect()
            ->route('admin.lepis.edit', $bulletin)
            ->with('success', 'Bulletin cree avec succes.');
    }

    public function edit(LepisBulletin $lepi)
    {
        return view('admin.lepis.edit', ['bulletin' => $lepi]);
    }

    public function update(Request $request, LepisBulletin $lepi)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'issue_number' => 'required|integer|min:1',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'year' => 'required|integer|min:1900|max:2100',
            'pdf' => 'nullable|file|mimes:pdf|max:51200',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('pdf')) {
            // Remove old PDF
            if ($lepi->pdf_path) {
                Storage::disk('public')->delete($lepi->pdf_path);
            }
            $validated['pdf_path'] = $request->file('pdf')->store('lepis', 'public');
        }

        unset($validated['pdf']);

        $lepi->update($validated);

        return redirect()
            ->route('admin.lepis.edit', $lepi)
            ->with('success', 'Bulletin mis a jour.');
    }

    public function destroy(LepisBulletin $lepi)
    {
        if ($lepi->pdf_path) {
            Storage::disk('public')->delete($lepi->pdf_path);
        }

        $lepi->delete();

        return redirect()
            ->route('admin.lepis.index')
            ->with('success', 'Bulletin supprime.');
    }

    public function togglePublish(LepisBulletin $bulletin)
    {
        $bulletin->is_published = !$bulletin->is_published;
        $bulletin->published_at = $bulletin->is_published ? now() : null;
        $bulletin->save();

        $status = $bulletin->is_published ? 'publie' : 'depublie';

        return back()->with('success', "Bulletin {$status}.");
    }
}
