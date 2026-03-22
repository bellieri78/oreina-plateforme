<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['member', 'product']);

        // Search by member name
        if ($search = $request->get('search')) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->get('product_id'));
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->get('source'));
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->get('date_to'));
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('purchase_date', $request->get('year'));
        }

        // Sorting
        $sortField = $request->get('sort', 'purchase_date');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['purchase_date', 'total_amount', 'created_at'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('purchase_date', 'desc');
        }

        // Get data for filters
        $products = Product::orderBy('name')->pluck('name', 'id');
        $paymentMethods = Purchase::distinct()->whereNotNull('payment_method')->pluck('payment_method')->sort();
        $years = Purchase::selectRaw('EXTRACT(YEAR FROM purchase_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $purchases = $query->paginate(20)->withQueryString();

        // Stats
        $totalPurchases = Purchase::count();
        $yearAmount = Purchase::whereYear('purchase_date', now()->year)->sum('total_amount');
        $importCount = Purchase::where('source', 'import')->count();
        $manualCount = Purchase::where('source', 'manual')->count();

        return view('admin.purchases.index', compact(
            'purchases', 'totalPurchases', 'yearAmount', 'importCount', 'manualCount',
            'products', 'paymentMethods', 'years'
        ));
    }

    public function create()
    {
        $members = Member::orderBy('last_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.purchases.create', compact('members', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['total_amount'] = $validated['unit_price'] * $validated['quantity'];
        $validated['source'] = 'manual';

        Purchase::create($validated);

        return redirect()
            ->route('admin.purchases.index')
            ->with('success', 'Achat enregistre avec succes.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['member', 'product', 'legacyMembership']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $members = Member::orderBy('last_name')->get();
        $products = Product::orderBy('name')->get();
        return view('admin.purchases.edit', compact('purchase', 'members', 'products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['total_amount'] = $validated['unit_price'] * $validated['quantity'];

        $purchase->update($validated);

        return redirect()
            ->route('admin.purchases.index')
            ->with('success', 'Achat mis a jour.');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('admin.purchases.index')->with('success', 'Achat supprime.');
    }

    public function export(Request $request)
    {
        $query = Purchase::with(['member', 'product']);

        if ($search = $request->get('search')) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->get('product_id'));
        }

        if ($request->filled('source')) {
            $query->where('source', $request->get('source'));
        }

        if ($request->filled('year')) {
            $query->whereYear('purchase_date', $request->get('year'));
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="achats_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Membre', 'Email', 'Produit', 'Quantite', 'Prix unitaire', 'Total', 'Date', 'Paiement', 'Source'];

        $callback = function () use ($purchases, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($purchases as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->member ? $p->member->full_name : '-',
                    $p->member ? $p->member->email : '-',
                    $p->product ? $p->product->name : '-',
                    $p->quantity,
                    number_format($p->unit_price, 2, ',', ' ') . ' EUR',
                    number_format($p->total_amount, 2, ',', ' ') . ' EUR',
                    $p->purchase_date?->format('d/m/Y'),
                    $p->payment_method ?? '-',
                    $p->getSourceLabel(),
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->get('ids'));
        $deleted = Purchase::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.purchases.index')
            ->with('success', "{$deleted} achat(s) supprime(s) avec succes.");
    }
}
