<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $query = Membership::with(['member', 'membershipType']);

        // Search by member name
        if ($search = $request->get('search')) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where('end_date', '>=', now());
            } elseif ($request->get('status') === 'expired') {
                $query->where('end_date', '<', now());
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('membership_type_id', $request->get('type'));
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->get('date_to'));
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->get('year'));
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['created_at', 'start_date', 'end_date', 'amount_paid'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get data for filters
        $types = MembershipType::orderBy('name')->pluck('name', 'id');
        $paymentMethods = Membership::distinct()->whereNotNull('payment_method')->pluck('payment_method')->sort();
        $years = Membership::selectRaw('EXTRACT(YEAR FROM start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $memberships = $query->paginate(20)->withQueryString();

        // Stats
        $activeCount = Membership::where('end_date', '>=', now())->count();
        $expiredCount = Membership::where('end_date', '<', now())->count();
        $yearAmount = Membership::whereYear('start_date', now()->year)->sum('amount_paid');

        return view('admin.memberships.index', compact(
            'memberships', 'activeCount', 'expiredCount', 'yearAmount',
            'types', 'paymentMethods', 'years'
        ));
    }

    public function create()
    {
        $members = Member::orderBy('last_name')->get();
        $membershipTypes = MembershipType::ordered()->get();
        return view('admin.memberships.create', compact('members', 'membershipTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'membership_type_id' => 'nullable|exists:membership_types,id',
            'amount_paid' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $membership = Membership::create($validated);

        return redirect()
            ->route('admin.memberships.index')
            ->with('success', 'Adhesion creee avec succes.');
    }

    public function show(Membership $membership)
    {
        $membership->load(['member', 'membershipType']);
        return view('admin.memberships.show', compact('membership'));
    }

    public function edit(Membership $membership)
    {
        $membership->load('membershipType');
        $members = Member::orderBy('last_name')->get();
        $membershipTypes = MembershipType::ordered()->get();
        return view('admin.memberships.edit', compact('membership', 'members', 'membershipTypes'));
    }

    public function update(Request $request, Membership $membership)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'membership_type_id' => 'nullable|exists:membership_types,id',
            'amount_paid' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $membership->update($validated);

        return redirect()
            ->route('admin.memberships.index')
            ->with('success', 'Adhesion mise a jour.');
    }

    public function destroy(Membership $membership)
    {
        $membership->delete();
        return redirect()->route('admin.memberships.index')->with('success', 'Adhesion supprimee.');
    }

    /**
     * Export memberships to CSV
     */
    public function export(Request $request)
    {
        $query = Membership::with(['member', 'membershipType']);

        // Apply same filters as index
        if ($search = $request->get('search')) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where('end_date', '>=', now());
            } elseif ($request->get('status') === 'expired') {
                $query->where('end_date', '<', now());
            }
        }

        if ($request->filled('type')) {
            $query->where('membership_type_id', $request->get('type'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->get('date_to'));
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->get('year'));
        }

        // Export selected IDs only
        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $memberships = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="adhesions_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Adherent', 'Email', 'Type', 'Montant', 'Debut', 'Fin', 'Statut', 'Mode paiement', 'Reference'];

        $callback = function () use ($memberships, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($memberships as $m) {
                $status = $m->end_date >= now() ? 'Active' : 'Expiree';
                fputcsv($file, [
                    $m->id,
                    $m->member ? $m->member->full_name : '-',
                    $m->member ? $m->member->email : '-',
                    $m->membershipType?->name ?? '-',
                    number_format($m->amount_paid, 2, ',', ' ') . ' EUR',
                    $m->start_date?->format('d/m/Y'),
                    $m->end_date?->format('d/m/Y'),
                    $status,
                    $m->payment_method,
                    $m->payment_reference,
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Bulk delete memberships
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->get('ids'));
        $deleted = Membership::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.memberships.index')
            ->with('success', "{$deleted} adhesion(s) supprimee(s) avec succes.");
    }
}
