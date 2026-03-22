<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\BrevoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BrevoController extends Controller
{
    protected BrevoService $brevo;

    public function __construct(BrevoService $brevo)
    {
        $this->brevo = $brevo;
    }

    /**
     * Brevo dashboard
     */
    public function index()
    {
        $isConfigured = $this->brevo->isConfigured();
        $lists = [];
        $account = null;
        $error = null;

        if ($isConfigured) {
            $listsResponse = $this->brevo->getLists();
            if ($listsResponse['success']) {
                $lists = $listsResponse['data'];
            } else {
                $error = $listsResponse['error'];
            }
        }

        // Stats
        $stats = [
            'total_members' => Member::count(),
            'with_email' => Member::whereNotNull('email')->where('email', '!=', '')->count(),
            'newsletter_subscribers' => Member::where('newsletter_subscribed', true)->whereNotNull('email')->count(),
            'active_members' => Member::whereHas('memberships', fn($q) => $q->where('end_date', '>=', now()))->count(),
        ];

        // Sync history
        $syncHistory = Cache::get('brevo_sync_history', []);

        return view('admin.brevo.index', compact(
            'isConfigured',
            'lists',
            'stats',
            'syncHistory',
            'error'
        ));
    }

    /**
     * Create a new list
     */
    public function createList(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $result = $this->brevo->createList($validated['name']);

        if ($result['success']) {
            return redirect()
                ->route('admin.brevo.index')
                ->with('success', 'Liste "' . $validated['name'] . '" creee avec succes.');
        }

        return redirect()
            ->route('admin.brevo.index')
            ->with('error', 'Erreur lors de la creation : ' . $result['error']);
    }

    /**
     * Sync members to a list
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'list_id' => 'required|integer',
            'sync_type' => 'required|in:all,newsletter,active',
        ]);

        $listId = $validated['list_id'];
        $syncType = $validated['sync_type'];

        // Get members based on sync type
        $query = Member::whereNotNull('email')->where('email', '!=', '');

        switch ($syncType) {
            case 'newsletter':
                $query->where('newsletter_subscribed', true);
                $label = 'Abonnes newsletter';
                break;
            case 'active':
                $query->whereHas('memberships', fn($q) => $q->where('end_date', '>=', now()));
                $label = 'Adherents actifs';
                break;
            default:
                $label = 'Tous les contacts';
        }

        $members = $query->get();

        if ($members->isEmpty()) {
            return redirect()
                ->route('admin.brevo.index')
                ->with('info', 'Aucun contact a synchroniser.');
        }

        // Perform sync
        $result = $this->brevo->importContacts($members, $listId);

        // Log sync history
        $history = Cache::get('brevo_sync_history', []);
        array_unshift($history, [
            'date' => now()->format('d/m/Y H:i'),
            'type' => $label,
            'count' => $result['success'] ? $result['count'] : 0,
            'success' => $result['success'],
            'error' => $result['error'] ?? null,
        ]);
        $history = array_slice($history, 0, 20); // Keep last 20
        Cache::put('brevo_sync_history', $history, now()->addDays(30));

        if ($result['success']) {
            return redirect()
                ->route('admin.brevo.index')
                ->with('success', $result['count'] . ' contact(s) synchronise(s) avec succes.');
        }

        return redirect()
            ->route('admin.brevo.index')
            ->with('error', 'Erreur lors de la synchronisation : ' . $result['error']);
    }

    /**
     * Export members to create a new list
     */
    public function exportToNewList(Request $request)
    {
        $validated = $request->validate([
            'list_name' => 'required|string|max:255',
            'sync_type' => 'required|in:all,newsletter,active',
        ]);

        // Create list
        $listResult = $this->brevo->createList($validated['list_name']);

        if (!$listResult['success']) {
            return redirect()
                ->route('admin.brevo.index')
                ->with('error', 'Erreur lors de la creation de la liste : ' . $listResult['error']);
        }

        $listId = $listResult['data']['id'];

        // Redirect to sync with the new list
        return $this->sync(new Request([
            'list_id' => $listId,
            'sync_type' => $validated['sync_type'],
        ]));
    }

    /**
     * View list details
     */
    public function showList(int $listId)
    {
        if (!$this->brevo->isConfigured()) {
            return redirect()->route('admin.brevo.index');
        }

        // Get list contacts
        $contacts = [];
        $totalContacts = 0;

        try {
            $response = $this->brevo->getContact('test@example.com');
            // Note: Getting all contacts requires pagination
        } catch (\Exception $e) {
            // Ignore
        }

        $lists = $this->brevo->getLists();
        $list = collect($lists['data'] ?? [])->firstWhere('id', $listId);

        return view('admin.brevo.list', compact('list', 'contacts', 'totalContacts'));
    }

    /**
     * Import contacts from a Brevo list
     */
    public function importFromList(Request $request)
    {
        $validated = $request->validate([
            'list_id' => 'required|integer',
            'import_mode' => 'required|in:create_only,update_only,create_update',
        ]);

        $listId = $validated['list_id'];
        $importMode = $validated['import_mode'];

        // Get list name for tagging
        $listsResult = $this->brevo->getLists();
        $listName = null;
        if ($listsResult['success']) {
            $list = collect($listsResult['data'])->firstWhere('id', $listId);
            $listName = $list['name'] ?? null;
        }

        // Get contacts from Brevo
        $result = $this->brevo->getAllContactsFromList($listId);

        if (!$result['success']) {
            return redirect()
                ->route('admin.brevo.index')
                ->with('error', 'Erreur lors de la recuperation des contacts : ' . $result['error']);
        }

        $contacts = $result['data'];

        if (empty($contacts)) {
            return redirect()
                ->route('admin.brevo.index')
                ->with('info', 'Aucun contact dans cette liste.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        // Build tag name from list name (prefix with "brevo:")
        $tagName = $listName ? "brevo:{$listName}" : null;

        foreach ($contacts as $contact) {
            $email = $contact['email'] ?? null;
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }

            $attributes = $contact['attributes'] ?? [];

            $existingMember = Member::where('email', strtolower(trim($email)))->first();

            if ($existingMember) {
                // Member exists
                if ($importMode === 'create_only') {
                    // Still add the tag even if we skip the update
                    if ($tagName) {
                        $existingMember->addTag($tagName, 'brevo');
                    }
                    $skipped++;
                    continue;
                }

                // Update existing member
                $existingMember->update($this->mapBrevoAttributesToMember($attributes));

                // Add tag
                if ($tagName) {
                    $existingMember->addTag($tagName, 'brevo');
                }

                $updated++;
            } else {
                // New member
                if ($importMode === 'update_only') {
                    $skipped++;
                    continue;
                }

                // Create new member
                $newMember = Member::create(array_merge(
                    [
                        'email' => strtolower(trim($email)),
                        'member_number' => Member::generateMemberNumber(),
                    ],
                    $this->mapBrevoAttributesToMember($attributes)
                ));

                // Add tag
                if ($tagName) {
                    $newMember->addTag($tagName, 'brevo');
                }

                $created++;
            }
        }

        // Log import history
        $history = \Illuminate\Support\Facades\Cache::get('brevo_sync_history', []);
        array_unshift($history, [
            'date' => now()->format('d/m/Y H:i'),
            'type' => 'Import depuis Brevo' . ($listName ? " ({$listName})" : ''),
            'count' => $created + $updated,
            'success' => true,
            'details' => "{$created} crees, {$updated} MAJ, {$skipped} ignores" . ($tagName ? ", tag: {$tagName}" : ''),
        ]);
        $history = array_slice($history, 0, 20);
        \Illuminate\Support\Facades\Cache::put('brevo_sync_history', $history, now()->addDays(30));

        $message = "Import termine : {$created} contact(s) cree(s), {$updated} mis a jour, {$skipped} ignore(s).";
        if ($tagName) {
            $message .= " Tag \"{$tagName}\" ajoute aux contacts importes.";
        }

        return redirect()
            ->route('admin.brevo.index')
            ->with('success', $message);
    }

    /**
     * Map Brevo attributes to Member model fields
     */
    protected function mapBrevoAttributesToMember(array $attributes): array
    {
        $data = [];

        // Map standard Brevo attributes
        $mapping = [
            'PRENOM' => 'first_name',
            'FIRSTNAME' => 'first_name',
            'NOM' => 'last_name',
            'LASTNAME' => 'last_name',
            'VILLE' => 'city',
            'CITY' => 'city',
            'CODE_POSTAL' => 'postal_code',
            'ZIPCODE' => 'postal_code',
            'PAYS' => 'country',
            'COUNTRY' => 'country',
            'TELEPHONE' => 'phone',
            'PHONE' => 'phone',
            'SMS' => 'phone',
            'ADRESSE' => 'address',
            'ADDRESS' => 'address',
        ];

        foreach ($mapping as $brevoKey => $memberField) {
            if (!empty($attributes[$brevoKey])) {
                $data[$memberField] = $attributes[$brevoKey];
            }
        }

        // Newsletter subscription
        if (isset($attributes['NEWSLETTER'])) {
            $data['newsletter_subscribed'] = (bool) $attributes['NEWSLETTER'];
        }

        return $data;
    }

    /**
     * Test connection
     */
    public function testConnection()
    {
        if (!$this->brevo->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'Cle API non configuree',
            ]);
        }

        $result = $this->brevo->getLists();

        return response()->json([
            'success' => $result['success'],
            'error' => $result['error'] ?? null,
            'lists_count' => count($result['data'] ?? []),
        ]);
    }
}
