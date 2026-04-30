<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query();

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->get('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', 'ilike', "%{$request->get('city')}%");
        }

        // Filter by country
        if ($request->filled('country')) {
            $query->where('country', $request->get('country'));
        }

        // Filter by date range (created_at)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Filter by membership status
        if ($request->filled('membership')) {
            if ($request->get('membership') === 'active') {
                $query->whereHas('memberships', function ($q) {
                    $q->where('status', 'active');
                });
            } elseif ($request->get('membership') === 'expired') {
                $query->whereHas('memberships', function ($q) {
                    $q->where('status', 'expired');
                });
            } elseif ($request->get('membership') === 'none') {
                $query->whereDoesntHave('memberships');
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'last_name');
        $sortDirection = $request->get('direction', 'asc');
        $allowedSorts = ['last_name', 'first_name', 'email', 'city', 'created_at'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('last_name');
        }

        // Get unique countries for filter dropdown
        $countries = Member::distinct()->whereNotNull('country')->pluck('country')->sort();

        $members = $query->paginate(20)->withQueryString();

        return view('admin.members.index', compact('members', 'countries'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $member = Member::create($validated);

        return redirect()
            ->route('admin.members.show', $member)
            ->with('success', 'Contact cree avec succes.');
    }

    public function show(Member $member)
    {
        $member->load(['memberships', 'donations', 'consents', 'lepisBulletinRecipients.bulletin']);

        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $member->update($validated);

        return redirect()
            ->route('admin.members.show', $member)
            ->with('success', 'Contact mis a jour avec succes.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Contact supprime avec succes.');
    }

    /**
     * Export members to CSV
     */
    public function export(Request $request)
    {
        $query = Member::query();

        // Apply same filters as index
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->get('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('city')) {
            $query->where('city', 'ilike', "%{$request->get('city')}%");
        }

        if ($request->filled('country')) {
            $query->where('country', $request->get('country'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Export selected IDs only
        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $members = $query->orderBy('last_name')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="adherents_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Nom', 'Prenom', 'Email', 'Telephone', 'Adresse', 'Code postal', 'Ville', 'Pays', 'Actif', 'Date creation'];

        $callback = function () use ($members, $columns) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($members as $member) {
                fputcsv($file, [
                    $member->id,
                    $member->last_name,
                    $member->first_name,
                    $member->email,
                    $member->phone,
                    $member->address,
                    $member->postal_code,
                    $member->city,
                    $member->country,
                    $member->is_active ? 'Oui' : 'Non',
                    $member->created_at?->format('d/m/Y'),
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('admin.members.import');
    }

    /**
     * Process CSV import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $imported = 0;
        $errors = [];
        $row = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }

            // Read header row
            $header = fgetcsv($handle, 0, ';');
            if (!$header) {
                $header = fgetcsv($handle, 0, ',');
                rewind($handle);
                fgetcsv($handle, 0, ','); // Skip header again
            }

            // Map header to expected fields
            $headerMap = $this->mapHeaders($header);

            while (($data = fgetcsv($handle, 0, ';')) !== false) {
                $row++;

                if (count($data) < 3) {
                    // Try comma separator
                    $data = str_getcsv(implode(';', $data), ',');
                }

                try {
                    $memberData = $this->extractMemberData($data, $headerMap);

                    if (empty($memberData['email'])) {
                        $errors[] = "Ligne {$row}: Email manquant";
                        continue;
                    }

                    // Check if member exists
                    $existingMember = Member::where('email', $memberData['email'])->first();

                    if ($existingMember) {
                        // Update existing
                        $existingMember->update(array_filter($memberData));
                    } else {
                        // Create new
                        Member::create($memberData);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne {$row}: " . $e->getMessage();
                }
            }

            fclose($handle);
        }

        $message = "{$imported} contact(s) importe(s) avec succes.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " erreur(s).";
        }

        return redirect()
            ->route('admin.members.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    /**
     * Map CSV headers to database fields
     */
    private function mapHeaders(array $header): array
    {
        $map = [];
        $headerLower = array_map('strtolower', array_map('trim', $header));

        $mappings = [
            'last_name' => ['nom', 'last_name', 'lastname', 'nom de famille'],
            'first_name' => ['prenom', 'first_name', 'firstname', 'prénom'],
            'email' => ['email', 'e-mail', 'mail', 'courriel'],
            'phone' => ['telephone', 'phone', 'tel', 'téléphone', 'portable', 'mobile'],
            'address' => ['adresse', 'address', 'rue', 'voie'],
            'postal_code' => ['code postal', 'postal_code', 'cp', 'code_postal', 'zip'],
            'city' => ['ville', 'city', 'commune'],
            'country' => ['pays', 'country'],
        ];

        foreach ($mappings as $field => $variants) {
            foreach ($variants as $variant) {
                $index = array_search($variant, $headerLower);
                if ($index !== false) {
                    $map[$field] = $index;
                    break;
                }
            }
        }

        return $map;
    }

    /**
     * Extract member data from CSV row
     */
    private function extractMemberData(array $data, array $headerMap): array
    {
        $memberData = [
            'is_active' => true,
        ];

        foreach ($headerMap as $field => $index) {
            if (isset($data[$index])) {
                $memberData[$field] = trim($data[$index]);
            }
        }

        return $memberData;
    }

    /**
     * Bulk delete members
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->get('ids'));
        $deleted = Member::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.members.index')
            ->with('success', "{$deleted} contact(s) supprime(s) avec succes.");
    }

    /**
     * Bulk activate/deactivate members
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'status' => 'required|in:activate,deactivate',
        ]);

        $ids = explode(',', $request->get('ids'));
        $isActive = $request->get('status') === 'activate';

        $updated = Member::whereIn('id', $ids)->update(['is_active' => $isActive]);

        $action = $isActive ? 'active(s)' : 'desactive(s)';
        return redirect()
            ->route('admin.members.index')
            ->with('success', "{$updated} contact(s) {$action} avec succes.");
    }
}
