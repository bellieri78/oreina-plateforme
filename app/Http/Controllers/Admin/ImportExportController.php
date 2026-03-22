<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExportLog;
use App\Models\ExportTemplate;
use App\Models\ImportLog;
use App\Models\ImportTemplate;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ImportExportController extends Controller
{
    /**
     * Import/Export dashboard
     */
    public function index()
    {
        $importTemplates = ImportTemplate::with('creator')->orderBy('name')->get();
        $exportTemplates = ExportTemplate::with('creator')->orderBy('name')->get();
        $recentImports = ImportLog::with(['user', 'template'])->recent()->take(10)->get();
        $recentExports = ExportLog::with(['user', 'template'])->recent()->take(10)->get();

        // Stats
        $stats = [
            'total_imports' => ImportLog::count(),
            'successful_imports' => ImportLog::completed()->count(),
            'total_exports' => ExportLog::count(),
            'import_templates' => ImportTemplate::count(),
            'export_templates' => ExportTemplate::count(),
        ];

        return view('admin.import-export.index', compact(
            'importTemplates',
            'exportTemplates',
            'recentImports',
            'recentExports',
            'stats'
        ));
    }

    // ===== IMPORT TEMPLATES =====

    /**
     * Create import template form
     */
    public function createImportTemplate()
    {
        $types = ImportTemplate::getTypes();

        return view('admin.import-export.import-template-create', compact('types'));
    }

    /**
     * Store import template
     */
    public function storeImportTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:members,memberships,donations',
            'description' => 'nullable|string',
            'mapping' => 'required|array',
            'is_default' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();

        // If marking as default, unset other defaults
        if ($request->boolean('is_default')) {
            ImportTemplate::where('type', $validated['type'])->update(['is_default' => false]);
        }

        ImportTemplate::create($validated);

        return redirect()
            ->route('admin.import-export.index')
            ->with('success', 'Modele d\'import cree avec succes.');
    }

    /**
     * Edit import template
     */
    public function editImportTemplate(ImportTemplate $template)
    {
        $types = ImportTemplate::getTypes();
        $defaultMapping = ImportTemplate::getDefaultMapping($template->type);

        return view('admin.import-export.import-template-edit', compact('template', 'types', 'defaultMapping'));
    }

    /**
     * Update import template
     */
    public function updateImportTemplate(Request $request, ImportTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'mapping' => 'required|array',
            'is_default' => 'boolean',
        ]);

        // If marking as default, unset other defaults
        if ($request->boolean('is_default') && !$template->is_default) {
            ImportTemplate::where('type', $template->type)->update(['is_default' => false]);
        }

        $template->update($validated);

        return redirect()
            ->route('admin.import-export.index')
            ->with('success', 'Modele d\'import mis a jour.');
    }

    /**
     * Delete import template
     */
    public function destroyImportTemplate(ImportTemplate $template)
    {
        $template->delete();

        return redirect()
            ->route('admin.import-export.index')
            ->with('success', 'Modele d\'import supprime.');
    }

    // ===== EXPORT TEMPLATES =====

    /**
     * Create export template form
     */
    public function createExportTemplate()
    {
        $types = ExportTemplate::getTypes();

        return view('admin.import-export.export-template-create', compact('types'));
    }

    /**
     * Store export template
     */
    public function storeExportTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:members,memberships,donations,volunteer',
            'description' => 'nullable|string',
            'columns' => 'required|array',
            'is_default' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();

        // If marking as default, unset other defaults
        if ($request->boolean('is_default')) {
            ExportTemplate::where('type', $validated['type'])->update(['is_default' => false]);
        }

        ExportTemplate::create($validated);

        return redirect()
            ->route('admin.import-export.index')
            ->with('success', 'Modele d\'export cree avec succes.');
    }

    /**
     * Edit export template
     */
    public function editExportTemplate(ExportTemplate $template)
    {
        $types = ExportTemplate::getTypes();
        $availableColumns = ExportTemplate::getAvailableColumns($template->type);

        return view('admin.import-export.export-template-edit', compact('template', 'types', 'availableColumns'));
    }

    /**
     * Update export template
     */
    public function updateExportTemplate(Request $request, ExportTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'columns' => 'required|array',
            'is_default' => 'boolean',
        ]);

        // If marking as default, unset other defaults
        if ($request->boolean('is_default') && !$template->is_default) {
            ExportTemplate::where('type', $template->type)->update(['is_default' => false]);
        }

        $template->update($validated);

        return redirect()
            ->route('admin.import-export.index')
            ->with('success', 'Modele d\'export mis a jour.');
    }

    /**
     * Delete export template
     */
    public function destroyExportTemplate(ExportTemplate $template)
    {
        $template->delete();

        return redirect()
            ->route('admin.import-export.index')
            ->with('success', 'Modele d\'export supprime.');
    }

    // ===== IMPORT HISTORY =====

    /**
     * View import log details
     */
    public function showImportLog(ImportLog $log)
    {
        $log->load(['user', 'template']);

        return view('admin.import-export.import-log', compact('log'));
    }

    // ===== EXPORT WITH TEMPLATE =====

    /**
     * Export using template
     */
    public function exportWithTemplate(Request $request, ExportTemplate $template)
    {
        $type = $template->type;
        $columns = $template->columns;

        // Get data based on type
        $query = match ($type) {
            'members' => Member::query(),
            'memberships' => Membership::with('member'),
            'donations' => Donation::with('member'),
            default => null,
        };

        if (!$query) {
            return back()->with('error', 'Type d\'export invalide.');
        }

        // Apply filters if any
        if ($request->filled('filters')) {
            // Apply custom filters from request
        }

        $data = $query->get();

        // Log export
        ExportLog::create([
            'type' => $type,
            'filename' => "{$type}_" . date('Y-m-d_His') . '.csv',
            'total_rows' => $data->count(),
            'columns' => $columns,
            'filters' => $request->get('filters'),
            'format' => 'csv',
            'user_id' => auth()->id(),
            'template_id' => $template->id,
        ]);

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$type}_" . date('Y-m-d') . ".csv\"",
        ];

        $callback = function () use ($data, $columns, $type) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

            // Get column labels
            $allColumns = ExportTemplate::getAvailableColumns($type);
            $headerRow = array_map(fn($col) => $allColumns[$col] ?? $col, $columns);
            fputcsv($file, $headerRow, ';');

            foreach ($data as $item) {
                $row = [];
                foreach ($columns as $col) {
                    $row[] = $this->getColumnValue($item, $col, $type);
                }
                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Get column value from model
     */
    protected function getColumnValue($item, string $column, string $type): string
    {
        return match ($column) {
            'member_name' => $item->member?->full_name ?? '',
            'member_email' => $item->member?->email ?? '',
            'status' => method_exists($item, 'getStatusLabelAttribute') ? $item->status_label : ($item->status ?? ''),
            'newsletter' => $item->newsletter ? 'Oui' : 'Non',
            'receipt_sent' => isset($item->receipt_sent) ? ($item->receipt_sent ? 'Oui' : 'Non') : '',
            'created_at' => $item->created_at?->format('d/m/Y H:i') ?? '',
            'start_date' => $item->start_date?->format('d/m/Y') ?? '',
            'end_date' => $item->end_date?->format('d/m/Y') ?? '',
            'donation_date' => $item->donation_date?->format('d/m/Y') ?? '',
            'amount' => number_format($item->amount ?? 0, 2, ',', ' '),
            default => $item->{$column} ?? '',
        };
    }

    /**
     * Get available columns for type (API)
     */
    public function getColumnsForType(string $type)
    {
        return response()->json(ExportTemplate::getAvailableColumns($type));
    }

    /**
     * Get default mapping for type (API)
     */
    public function getMappingForType(string $type)
    {
        return response()->json(ImportTemplate::getDefaultMapping($type));
    }
}
