<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Lepis\InvalidTransitionException;
use App\Exceptions\Lepis\MissingPdfException;
use App\Http\Controllers\Controller;
use App\Models\LepisBulletin;
use App\Services\LepisBulletinPublicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LepisBulletinController extends Controller
{
    public function __construct(private LepisBulletinPublicationService $publication) {}

    public function index(Request $request)
    {
        $query = LepisBulletin::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('title', 'ilike', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $sortField = $request->get('sort', 'year');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['title', 'issue_number', 'year', 'created_at', 'status'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('year', 'desc')->orderBy('issue_number', 'desc');
        }

        $bulletins = $query->paginate(15)->withQueryString();

        $stats = [
            'total'   => LepisBulletin::count(),
            'draft'   => LepisBulletin::where('status', 'draft')->count(),
            'members' => LepisBulletin::where('status', 'members')->count(),
            'public'  => LepisBulletin::where('status', 'public')->count(),
        ];

        return view('admin.lepis.index', compact('bulletins', 'stats'));
    }

    public function create()
    {
        $previous = LepisBulletin::orderBy('year', 'desc')
            ->orderBy('issue_number', 'desc')
            ->first();

        $nextIssue = ($previous?->issue_number ?? 0) + 1;

        $defaults = [
            'announcement_subject' => filled($previous?->announcement_subject)
                ? $previous->announcement_subject
                : str_replace('#[N]', '#' . $nextIssue, LepisBulletin::DEFAULT_ANNOUNCEMENT_SUBJECT),
            'announcement_body' => filled($previous?->announcement_body)
                ? $previous->announcement_body
                : LepisBulletin::DEFAULT_ANNOUNCEMENT_BODY,
        ];

        return view('admin.lepis.create', compact('defaults'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'issue_number' => 'required|integer|min:1',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'year' => 'required|integer|min:1900|max:2100',
            'summary' => 'nullable|string',
            'pdf' => 'required|file|mimes:pdf|max:51200',
            'cover' => 'nullable|image|max:5120',
            'announcement_subject' => 'nullable|string|max:255',
            'announcement_body' => 'nullable|string',
        ]);

        $validated['pdf_path'] = $request->file('pdf')->store('lepis', 'public');
        if ($request->hasFile('cover')) {
            $validated['cover_image'] = $request->file('cover')->store('lepis/covers', 'public');
        }
        unset($validated['pdf'], $validated['cover']);

        $bulletin = LepisBulletin::create($validated);

        return redirect()
            ->route('admin.lepis.edit', $bulletin)
            ->with('success', 'Bulletin créé. Complétez les informations puis publiez-le.');
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
            'summary' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:51200',
            'cover' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('pdf')) {
            if ($lepi->pdf_path) {
                Storage::disk('public')->delete($lepi->pdf_path);
            }
            $validated['pdf_path'] = $request->file('pdf')->store('lepis', 'public');
        }
        if ($request->hasFile('cover')) {
            if ($lepi->cover_image) {
                Storage::disk('public')->delete($lepi->cover_image);
            }
            $validated['cover_image'] = $request->file('cover')->store('lepis/covers', 'public');
        }
        unset($validated['pdf'], $validated['cover']);

        $lepi->update($validated);

        return redirect()
            ->route('admin.lepis.edit', $lepi)
            ->with('success', 'Bulletin mis à jour.');
    }

    public function destroy(LepisBulletin $lepi)
    {
        if (! $lepi->isDraft()) {
            return back()->with('error', "Impossible de supprimer un bulletin déjà publié. Revenez en brouillon avant suppression.");
        }

        if ($lepi->pdf_path) {
            Storage::disk('public')->delete($lepi->pdf_path);
        }
        if ($lepi->cover_image) {
            Storage::disk('public')->delete($lepi->cover_image);
        }
        $lepi->delete();

        return redirect()
            ->route('admin.lepis.index')
            ->with('success', 'Bulletin supprimé.');
    }

    public function publishToMembers(LepisBulletin $bulletin)
    {
        try {
            $this->publication->publishToMembers($bulletin);
        } catch (MissingPdfException|InvalidTransitionException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            // En sync mode, une exception du Job Brevo (ex: IP non whitelistée)
            // remonte ici. La transition en 'members' est déjà commitée et le
            // flag brevo_sync_failed a été posé par le failed() handler.
            // On laisse donc l'admin relancer depuis la carte Annonce.
            return back()->with(
                'error',
                "Bulletin publié aux adhérents, mais la synchronisation Brevo a échoué : {$e->getMessage()}. Relancez depuis la carte Annonce."
            );
        }

        return back()->with('success', 'Bulletin publié aux adhérents. Synchronisation Brevo en cours…');
    }

    public function makePublic(LepisBulletin $bulletin)
    {
        try {
            $this->publication->makePublic($bulletin);
        } catch (InvalidTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bulletin rendu public.');
    }

    public function revertToDraft(LepisBulletin $bulletin)
    {
        try {
            $this->publication->revertToDraft($bulletin);
        } catch (InvalidTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bulletin remis en brouillon.');
    }

    public function resyncBrevo(LepisBulletin $bulletin)
    {
        try {
            $this->publication->resyncBrevo($bulletin);
        } catch (InvalidTransitionException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                "La synchronisation Brevo a de nouveau échoué : {$e->getMessage()}."
            );
        }

        return back()->with('success', 'Relance de la synchronisation Brevo…');
    }

    public function updateAnnouncement(Request $request, LepisBulletin $bulletin)
    {
        $validated = $request->validate([
            'announcement_subject' => 'nullable|string|max:255',
            'announcement_body'    => 'nullable|string',
        ]);

        $bulletin->update($validated);

        return back()->with('success', "Template d'annonce enregistré.");
    }

    public function exportRecipients(LepisBulletin $bulletin, Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $format = $request->query('format', 'paper');
        abort_unless(in_array($format, ['paper', 'digital']), 400);

        $recipients = $bulletin->recipients()->where('format', $format)->with('member')->get();

        $filename = "lepis-{$bulletin->year}-{$bulletin->quarter}-{$format}.csv";

        return response()->streamDownload(function () use ($recipients) {
            $out = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['prenom', 'nom', 'email', 'adresse', 'code_postal', 'ville', 'pays', 'numero_adherent']);
            foreach ($recipients as $r) {
                $address = $r->postal_address_at_snapshot ?? [];
                fputcsv($out, [
                    $r->member->first_name ?? '',
                    $r->member->last_name ?? '',
                    $r->email_at_snapshot ?? '',
                    $address['address'] ?? '',
                    $address['postal_code'] ?? '',
                    $address['city'] ?? '',
                    $address['country'] ?? '',
                    $r->member->member_number ?? '',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function recalculateSnapshot(LepisBulletin $bulletin): \Illuminate\Http\RedirectResponse
    {
        $result = app(\App\Services\LepisBulletinRecipientSnapshotter::class)->snapshot($bulletin);

        $msg = "Snapshot recalcule : {$result->paperCount} papier, {$result->digitalCount} numerique";
        if (count($result->skipped) > 0) {
            $msg .= ", " . count($result->skipped) . " ecarte(s)";
        }

        return redirect()->route('admin.lepis.edit', $bulletin)->with('success', $msg);
    }
}
