<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Mail\LaboLepidoProposition;
use App\Models\FaqQuestion;
use App\Models\LepisBulletin;
use App\Models\MembershipType;
use App\Rules\TurnstileCaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function about()
    {
        return view('hub.pages.about');
    }

    public function contact()
    {
        return view('hub.pages.contact');
    }

    public function equipe()
    {
        return view('hub.pages.equipe');
    }

    public function pourquoi()
    {
        return view('hub.pages.pourquoi');
    }

    public function magazine()
    {
        return view('hub.pages.magazine');
    }

    public function projetTaxref()
    {
        return view('hub.pages.projet-taxref');
    }

    public function projetSeqref()
    {
        return view('hub.pages.projet-seqref');
    }

    public function projetBdc()
    {
        return view('hub.pages.projet-bdc');
    }

    public function projetIdent()
    {
        return view('hub.pages.projet-ident');
    }

    public function projetQualif()
    {
        return view('hub.pages.projet-qualif');
    }

    public function outilLaboLepidos()
    {
        return view('hub.pages.outil-labo-lepidos');
    }

    public function outilArtemisiae()
    {
        return view('hub.pages.outil-artemisiae');
    }

    public function proposerLaboLepidos(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:200',
            'email' => 'required|email|max:200',
            'type_proposition' => 'required|in:animer,suggerer',
            'sujet' => 'required|string|max:300',
            'motivation' => 'required|string|max:5000',
            'ressources' => 'nullable|string|max:5000',
            'rgpd' => 'accepted',
            'cf-turnstile-response' => ['nullable', new TurnstileCaptcha()],
        ]);

        // Trace dans les logs au cas où l'envoi mail échoue (rien ne se perd).
        Log::info('Proposition Labo Lépido reçue', $data);

        try {
            Mail::to('communication@oreina.org')->send(new LaboLepidoProposition($data));
        } catch (\Throwable $e) {
            Log::error('Envoi mail proposition Labo Lépido échoué', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
        }

        return redirect()
            ->route('hub.outils.labo-lepidos')
            ->with('labo_success', 'Votre proposition a bien été transmise au COTECH IDENT. Nous revenons vers vous dans les meilleurs délais.')
            ->withFragment('proposer');
    }

    public function faq()
    {
        $sections = config('faq.sections');
        $questionsBySection = FaqQuestion::visible()
            ->orderBy('sort_order')->orderBy('id')
            ->get()
            ->groupBy('section');

        return view('hub.pages.faq', compact('sections', 'questionsBySection'));
    }

    public function lepis()
    {
        $latestBulletins = LepisBulletin::visibleOnHub()
            ->orderBy('year', 'desc')
            ->orderBy('issue_number', 'desc')
            ->limit(3)
            ->get();

        return view('hub.pages.lepis', compact('latestBulletins'));
    }

    public function membership()
    {
        $membershipTypes = MembershipType::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('hub.pages.membership', compact('membershipTypes'));
    }
}
