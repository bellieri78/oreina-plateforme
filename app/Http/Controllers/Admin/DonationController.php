<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::with('member');

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('donor_name', 'ilike', "%{$search}%")
                  ->orWhere('donor_email', 'ilike', "%{$search}%");
            });
        }

        // Filter by receipt status
        if ($request->filled('receipt')) {
            $query->where('tax_receipt_sent', $request->get('receipt') === '1');
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // Filter by campaign
        if ($request->filled('campaign')) {
            $query->where('campaign', $request->get('campaign'));
        }

        // Filter by amount range
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->get('amount_min'));
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->get('amount_max'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('donation_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('donation_date', '<=', $request->get('date_to'));
        }

        // Filter by year (quick filter)
        if ($request->filled('year')) {
            $query->whereYear('donation_date', $request->get('year'));
        }

        // Sorting
        $sortField = $request->get('sort', 'donation_date');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['donation_date', 'amount', 'donor_name'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('donation_date', 'desc');
        }

        // Get data for filters
        $paymentMethods = Donation::distinct()->whereNotNull('payment_method')->pluck('payment_method')->sort();
        $campaigns = Donation::distinct()->whereNotNull('campaign')->pluck('campaign')->sort();
        $years = Donation::selectRaw('EXTRACT(YEAR FROM donation_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $donations = $query->paginate(20)->withQueryString();

        // Stats
        $totalAmount = Donation::sum('amount');
        $yearAmount = Donation::whereYear('donation_date', now()->year)->sum('amount');
        $filteredAmount = $query->sum('amount');

        return view('admin.donations.index', compact(
            'donations', 'totalAmount', 'yearAmount', 'filteredAmount',
            'paymentMethods', 'campaigns', 'years'
        ));
    }

    public function create()
    {
        $members = Member::orderBy('last_name')->get();
        return view('admin.donations.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email|max:255',
            'donor_address' => 'nullable|string|max:255',
            'donor_postal_code' => 'nullable|string|max:10',
            'donor_city' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'donation_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:255',
            'campaign' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $donation = Donation::create($validated);

        return redirect()
            ->route('admin.donations.show', $donation)
            ->with('success', 'Don enregistre avec succes.');
    }

    public function show(Donation $donation)
    {
        $donation->load('member');
        return view('admin.donations.show', compact('donation'));
    }

    public function edit(Donation $donation)
    {
        $members = Member::orderBy('last_name')->get();
        return view('admin.donations.edit', compact('donation', 'members'));
    }

    public function update(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email|max:255',
            'donor_address' => 'nullable|string|max:255',
            'donor_postal_code' => 'nullable|string|max:10',
            'donor_city' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'donation_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:255',
            'campaign' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tax_receipt_sent' => 'boolean',
        ]);

        $validated['tax_receipt_sent'] = $request->has('tax_receipt_sent');

        $donation->update($validated);

        return redirect()
            ->route('admin.donations.show', $donation)
            ->with('success', 'Don mis a jour avec succes.');
    }

    public function destroy(Donation $donation)
    {
        $donation->delete();
        return redirect()->route('admin.donations.index')->with('success', 'Don supprime.');
    }

    /**
     * Export donations to CSV
     */
    public function export(Request $request)
    {
        $query = Donation::with('member');

        // Apply same filters as index
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('donor_name', 'ilike', "%{$search}%")
                  ->orWhere('donor_email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('receipt')) {
            $query->where('tax_receipt_sent', $request->get('receipt') === '1');
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        if ($request->filled('campaign')) {
            $query->where('campaign', $request->get('campaign'));
        }

        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->get('amount_min'));
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->get('amount_max'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('donation_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('donation_date', '<=', $request->get('date_to'));
        }

        if ($request->filled('year')) {
            $query->whereYear('donation_date', $request->get('year'));
        }

        // Export selected IDs only
        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $donations = $query->orderBy('donation_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="dons_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Date', 'Donateur', 'Email', 'Montant', 'Mode paiement', 'Reference', 'Campagne', 'Recu fiscal', 'Adherent lie'];

        $callback = function () use ($donations, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($donations as $donation) {
                fputcsv($file, [
                    $donation->id,
                    $donation->donation_date?->format('d/m/Y'),
                    $donation->donor_name,
                    $donation->donor_email,
                    number_format($donation->amount, 2, ',', ' ') . ' EUR',
                    $donation->payment_method,
                    $donation->payment_reference,
                    $donation->campaign,
                    $donation->tax_receipt_sent ? 'Oui' : 'Non',
                    $donation->member ? $donation->member->full_name : '-',
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Bulk delete donations
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->get('ids'));
        $deleted = Donation::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.donations.index')
            ->with('success', "{$deleted} don(s) supprime(s) avec succes.");
    }

    /**
     * Bulk mark receipts as sent
     */
    public function bulkReceipt(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'status' => 'required|in:sent,not_sent',
        ]);

        $ids = explode(',', $request->get('ids'));
        $sent = $request->get('status') === 'sent';

        $updated = Donation::whereIn('id', $ids)->update(['tax_receipt_sent' => $sent]);

        $action = $sent ? 'marque(s) comme envoye(s)' : 'marque(s) comme non envoye(s)';
        return redirect()
            ->route('admin.donations.index')
            ->with('success', "{$updated} recu(s) fiscal(aux) {$action}.");
    }
}
