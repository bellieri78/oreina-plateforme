<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Models\LepisBulletin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LepisBulletinController extends Controller
{
    public function index()
    {
        $bulletins = LepisBulletin::visibleOnHub()
            ->orderBy('year', 'desc')
            ->orderBy('issue_number', 'desc')
            ->paginate(12);

        return view('hub.lepis.bulletins.index', compact('bulletins'));
    }

    public function show(LepisBulletin $bulletin)
    {
        abort_unless(
            $bulletin->isInMembersPhase() || $bulletin->isPublic(),
            404
        );

        $previous = LepisBulletin::visibleOnHub()
            ->where(function ($q) use ($bulletin) {
                $q->where('year', '<', $bulletin->year)
                  ->orWhere(function ($q) use ($bulletin) {
                      $q->where('year', $bulletin->year)->where('issue_number', '<', $bulletin->issue_number);
                  });
            })
            ->orderBy('year', 'desc')->orderBy('issue_number', 'desc')
            ->first();

        $next = LepisBulletin::visibleOnHub()
            ->where(function ($q) use ($bulletin) {
                $q->where('year', '>', $bulletin->year)
                  ->orWhere(function ($q) use ($bulletin) {
                      $q->where('year', $bulletin->year)->where('issue_number', '>', $bulletin->issue_number);
                  });
            })
            ->orderBy('year', 'asc')->orderBy('issue_number', 'asc')
            ->first();

        return view('hub.lepis.bulletins.show', compact('bulletin', 'previous', 'next'));
    }

    public function download(Request $request, LepisBulletin $bulletin)
    {
        // Une URL signée valide (typiquement envoyée par email depuis Brevo)
        // permet l'accès au PDF sans être connecté. Sinon la policy s'applique.
        if (! $request->hasValidSignature()) {
            $this->authorize('download', $bulletin);
        }

        if (! $bulletin->pdf_path || ! Storage::disk('public')->exists($bulletin->pdf_path)) {
            abort(404, 'PDF non trouvé');
        }

        return Storage::disk('public')->download(
            $bulletin->pdf_path,
            "Lepis_{$bulletin->issue_number}_{$bulletin->quarter}_{$bulletin->year}.pdf"
        );
    }
}
