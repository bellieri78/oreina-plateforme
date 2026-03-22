<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Search by name
        if ($search = $request->get('search')) {
            $query->where('name', 'ilike', "%{$search}%");
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('product_type', $request->get('type'));
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->where('year', $request->get('year'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['created_at', 'name', 'price', 'year'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get data for filters
        $years = Product::whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $products = $query->withCount('purchases')->paginate(20)->withQueryString();

        // Stats
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalSales = \App\Models\Purchase::sum('total_amount');
        $magazineCount = Product::where('product_type', 'magazine')->count();

        return view('admin.products.index', compact(
            'products', 'totalProducts', 'activeProducts', 'totalSales', 'magazineCount', 'years'
        ));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'product_type' => 'required|in:magazine,hors_serie,rencontre,autre',
            'sku' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'year' => 'nullable|integer|min:2000|max:2100',
            'issue_number' => 'nullable|integer',
            'event_date' => 'nullable|date',
            'event_location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produit cree avec succes.');
    }

    public function show(Product $product)
    {
        $product->load('purchases.member');
        $recentPurchases = $product->purchases()->with('member')->latest()->take(10)->get();
        $totalRevenue = $product->purchases()->sum('total_amount');
        $purchaseCount = $product->purchases()->count();

        return view('admin.products.show', compact('product', 'recentPurchases', 'totalRevenue', 'purchaseCount'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'product_type' => 'required|in:magazine,hors_serie,rencontre,autre',
            'sku' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'year' => 'nullable|integer|min:2000|max:2100',
            'issue_number' => 'nullable|integer',
            'event_date' => 'nullable|date',
            'event_location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produit mis a jour.');
    }

    public function destroy(Product $product)
    {
        if ($product->purchases()->exists()) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Impossible de supprimer ce produit car il a des achats associes.');
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produit supprime.');
    }

    public function export(Request $request)
    {
        $query = Product::withCount('purchases');

        if ($search = $request->get('search')) {
            $query->where('name', 'ilike', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->where('product_type', $request->get('type'));
        }

        if ($request->filled('year')) {
            $query->where('year', $request->get('year'));
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="produits_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Nom', 'Type', 'Annee', 'Prix', 'Ventes', 'Actif', 'Date creation'];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($products as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->name,
                    $p->getTypeLabel(),
                    $p->year ?? '-',
                    number_format($p->price, 2, ',', ' ') . ' EUR',
                    $p->purchases_count,
                    $p->is_active ? 'Oui' : 'Non',
                    $p->created_at?->format('d/m/Y'),
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
