<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected string $apiUrl = 'https://api.brevo.com/v3';
    protected ?string $apiKey = null;
    protected array $config = [];

    public function __construct()
    {
        $this->apiKey = config('brevo.api_key');
        $this->config = config('brevo') ?? [];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Create or update a contact in Brevo
     */
    public function upsertContact(Member $member, array $listIds = []): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        $attributes = $this->buildAttributes($member);

        $payload = [
            'email' => $member->email,
            'attributes' => $attributes,
            'updateEnabled' => config('brevo.sync.update_existing', true),
        ];

        if (!empty($listIds)) {
            $payload['listIds'] = array_map('intval', $listIds);
        }

        try {
            $response = $this->request('POST', '/contacts', $payload);

            Log::channel('daily')->info('Brevo contact upserted', [
                'email' => $member->email,
                'response' => $response,
            ]);

            return ['success' => true, 'data' => $response];
        } catch (\Exception $e) {
            Log::channel('daily')->error('Brevo upsert failed', [
                'email' => $member->email,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Batch import contacts to Brevo
     */
    public function importContacts(Collection $members, ?int $listId = null): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        $contacts = $members->map(function ($member) {
            return array_merge(
                ['email' => $member->email],
                $this->buildAttributes($member)
            );
        })->toArray();

        $payload = [
            'jsonBody' => $contacts,
            'updateExistingContacts' => config('brevo.sync.update_existing', true),
            'emptyContactsAttributes' => false,
        ];

        if ($listId) {
            $payload['listIds'] = [(int) $listId];
        }

        try {
            $response = $this->request('POST', '/contacts/import', $payload);

            Log::channel('daily')->info('Brevo batch import', [
                'count' => count($contacts),
                'list_id' => $listId,
                'response' => $response,
            ]);

            return ['success' => true, 'data' => $response, 'count' => count($contacts)];
        } catch (\Exception $e) {
            Log::channel('daily')->error('Brevo batch import failed', [
                'count' => count($contacts),
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete a contact from Brevo
     */
    public function deleteContact(string $email): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        try {
            $this->request('DELETE', '/contacts/' . urlencode($email));

            Log::channel('daily')->info('Brevo contact deleted', ['email' => $email]);

            return ['success' => true];
        } catch (\Exception $e) {
            Log::channel('daily')->error('Brevo delete failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Add contact to a list
     */
    public function addToList(string $email, int $listId): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        try {
            $this->request('POST', "/contacts/lists/{$listId}/contacts/add", [
                'emails' => [$email],
            ]);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Remove contact from a list
     */
    public function removeFromList(string $email, int $listId): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        try {
            $this->request('POST', "/contacts/lists/{$listId}/contacts/remove", [
                'emails' => [$email],
            ]);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get all lists from Brevo
     */
    public function getLists(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        try {
            $response = $this->request('GET', '/contacts/lists', [
                'limit' => 50,
                'offset' => 0,
            ]);

            return ['success' => true, 'data' => $response['lists'] ?? []];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create a new list in Brevo
     */
    public function createList(string $name, int $folderId = 1): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        try {
            $response = $this->request('POST', '/contacts/lists', [
                'name' => $name,
                'folderId' => $folderId,
            ]);

            Log::channel('daily')->info('Brevo list created', [
                'name' => $name,
                'id' => $response['id'] ?? null,
            ]);

            return ['success' => true, 'data' => $response];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get contact info from Brevo
     */
    public function getContact(string $email): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        try {
            $response = $this->request('GET', '/contacts/' . urlencode($email));

            return ['success' => true, 'data' => $response];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Update contact attributes
     */
    public function updateContact(string $email, array $attributes): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        try {
            $this->request('PUT', '/contacts/' . urlencode($email), [
                'attributes' => $attributes,
            ]);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Handle unsubscribe from Brevo webhook
     */
    public function handleUnsubscribe(string $email): void
    {
        $member = Member::where('email', $email)->first();

        if ($member) {
            $member->update(['newsletter_subscribed' => false]);

            Log::channel('daily')->info('Member unsubscribed via Brevo webhook', [
                'email' => $email,
                'member_id' => $member->id,
            ]);
        }
    }

    /**
     * Handle hard bounce from Brevo webhook
     */
    public function handleHardBounce(string $email): void
    {
        $member = Member::where('email', $email)->first();

        if ($member) {
            $member->update(['newsletter_subscribed' => false]);

            Log::channel('daily')->warning('Member hard bounced via Brevo', [
                'email' => $email,
                'member_id' => $member->id,
            ]);
        }
    }

    /**
     * Get contacts from a Brevo list
     */
    public function getContactsFromList(int $listId, int $limit = 500, int $offset = 0): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        try {
            $response = $this->request('GET', '/contacts', [
                'listIds' => $listId,
                'limit' => $limit,
                'offset' => $offset,
            ]);

            return [
                'success' => true,
                'data' => $response['contacts'] ?? [],
                'count' => $response['count'] ?? 0,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get all contacts from a list (handles pagination)
     */
    public function getAllContactsFromList(int $listId): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Brevo not configured'];
        }

        $allContacts = [];
        $offset = 0;
        $limit = 500;

        try {
            do {
                $response = $this->getContactsFromList($listId, $limit, $offset);

                if (!$response['success']) {
                    return $response;
                }

                $contacts = $response['data'];
                $allContacts = array_merge($allContacts, $contacts);
                $offset += $limit;

            } while (count($contacts) === $limit);

            return [
                'success' => true,
                'data' => $allContacts,
                'count' => count($allContacts),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Build Brevo attributes from Member model
     */
    protected function buildAttributes(Member $member): array
    {
        $currentMembership = $member->currentMembership();
        $hasDonations = $member->donations()->exists();

        return [
            'PRENOM' => $member->first_name ?? '',
            'NOM' => $member->last_name ?? '',
            'MEMBRE_NUMERO' => $member->member_number ?? '',
            'VILLE' => $member->city ?? '',
            'CODE_POSTAL' => $member->postal_code ?? '',
            'DATE_ADHESION' => $member->joined_at?->format('Y-m-d') ?? '',
            'DATE_EXPIRATION' => $member->membership_expires_at?->format('Y-m-d') ?? '',
            'EST_ADHERENT' => $currentMembership ? true : false,
            'EST_DONATEUR' => $hasDonations,
            'NEWSLETTER' => $member->newsletter_subscribed ?? false,
        ];
    }

    /**
     * Make HTTP request to Brevo API
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->apiUrl . $endpoint;

        $request = Http::withHeaders([
            'api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        // Disable SSL verification in local development (Windows/XAMPP)
        if (app()->environment('local')) {
            $request = $request->withoutVerifying();
        }

        $response = match (strtoupper($method)) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            'PUT' => $request->put($url, $data),
            'DELETE' => $request->delete($url),
            default => throw new \InvalidArgumentException("Invalid HTTP method: {$method}"),
        };

        if ($response->failed()) {
            $error = $response->json('message') ?? $response->body();
            throw new \Exception("Brevo API error: {$error}");
        }

        return $response->json() ?? [];
    }
}
