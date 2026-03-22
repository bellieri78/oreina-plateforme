<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class JournalIssueController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalIssue::withCount('submissions');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('volume_number', $search)
                  ->orWhere('issue_number', $search);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $issues = $query->orderBy('publication_date', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'total' => JournalIssue::count(),
            'published' => JournalIssue::where('status', 'published')->count(),
            'draft' => JournalIssue::where('status', 'draft')->count(),
        ];

        return view('admin.journal-issues.index', compact('issues', 'stats'));
    }

    public function create()
    {
        // Suggerer le prochain numero
        $lastIssue = JournalIssue::orderBy('volume_number', 'desc')
            ->orderBy('issue_number', 'desc')
            ->first();

        $suggestedVolume = $lastIssue ? $lastIssue->volume_number : 1;
        $suggestedIssue = $lastIssue ? $lastIssue->issue_number + 1 : 1;

        return view('admin.journal-issues.create', compact('suggestedVolume', 'suggestedIssue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'volume_number' => 'required|integer|min:1',
            'issue_number' => 'required|integer|min:1',
            'title' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:journal_issues,slug',
            'description' => 'nullable|string',
            'publication_date' => 'nullable|date',
            'status' => 'required|in:draft,published',
            'doi' => 'nullable|string|max:255',
            'page_count' => 'nullable|integer|min:1',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug("vol-{$validated['volume_number']}-num-{$validated['issue_number']}");
        }

        $issue = JournalIssue::create($validated);

        return redirect()
            ->route('admin.journal-issues.show', $issue)
            ->with('success', 'Numero cree avec succes.');
    }

    public function show(JournalIssue $journalIssue)
    {
        $journalIssue->load(['submissions' => function ($q) {
            $q->with('author')->orderBy('created_at', 'desc');
        }]);

        return view('admin.journal-issues.show', compact('journalIssue'));
    }

    public function edit(JournalIssue $journalIssue)
    {
        return view('admin.journal-issues.edit', compact('journalIssue'));
    }

    public function update(Request $request, JournalIssue $journalIssue)
    {
        $validated = $request->validate([
            'volume_number' => 'required|integer|min:1',
            'issue_number' => 'required|integer|min:1',
            'title' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:journal_issues,slug,' . $journalIssue->id,
            'description' => 'nullable|string',
            'publication_date' => 'nullable|date',
            'status' => 'required|in:draft,published',
            'doi' => 'nullable|string|max:255',
            'page_count' => 'nullable|integer|min:1',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug("vol-{$validated['volume_number']}-num-{$validated['issue_number']}");
        }

        $journalIssue->update($validated);

        return redirect()
            ->route('admin.journal-issues.show', $journalIssue)
            ->with('success', 'Numero mis a jour.');
    }

    public function destroy(JournalIssue $journalIssue)
    {
        if ($journalIssue->submissions()->count() > 0) {
            return redirect()
                ->route('admin.journal-issues.index')
                ->with('error', 'Impossible de supprimer un numero contenant des soumissions.');
        }

        $journalIssue->delete();
        return redirect()->route('admin.journal-issues.index')->with('success', 'Numero supprime.');
    }

    public function export(Request $request)
    {
        $query = JournalIssue::withCount('submissions');

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $issues = $query->orderBy('publication_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="numeros_revue_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Volume', 'Numero', 'Titre', 'Date publication', 'Statut', 'DOI', 'Nb articles'];

        $callback = function () use ($issues, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($issues as $i) {
                fputcsv($file, [
                    $i->id,
                    $i->volume_number,
                    $i->issue_number,
                    $i->title ?? '-',
                    $i->publication_date?->format('d/m/Y') ?? '-',
                    $i->status === 'published' ? 'Publie' : 'Brouillon',
                    $i->doi ?? '-',
                    $i->submissions_count,
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|string']);

        $ids = explode(',', $request->get('ids'));

        // Check for issues with submissions
        $hasSubmissions = JournalIssue::whereIn('id', $ids)->whereHas('submissions')->count();
        if ($hasSubmissions > 0) {
            return redirect()
                ->route('admin.journal-issues.index')
                ->with('error', 'Certains numeros contiennent des articles et ne peuvent pas etre supprimes.');
        }

        $deleted = JournalIssue::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.journal-issues.index')
            ->with('success', "{$deleted} numero(s) supprime(s).");
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $ids = explode(',', $request->get('ids'));
        $updated = JournalIssue::whereIn('id', $ids)->update(['status' => $request->get('status')]);

        return redirect()
            ->route('admin.journal-issues.index')
            ->with('success', "{$updated} numero(s) mis a jour.");
    }
}
