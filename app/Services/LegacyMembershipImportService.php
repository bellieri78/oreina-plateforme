<?php

namespace App\Services;

use App\Models\ImportLog;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LegacyMembershipImportService
{
    protected MembershipType $legacyMembershipType;
    protected array $errors = [];
    protected array $stats = [
        'members_created' => 0,
        'members_updated' => 0,
        'memberships_created' => 0,
        'purchases_created' => 0,
        'rows_skipped' => 0,
        'rows_processed' => 0,
    ];

    protected array $organizationPatterns = [
        '/^Association\s/i',
        '/^ANA-CEN\s/i',
        '/^ANEPE\s/i',
        '/^ANNAM\s/i',
        '/\(Association[^)]*\)/i',
        '/Bibliothèque/i',
        '/^Museum\s/i',
        '/^Centre\s/i',
        '/^Conservatoire\s/i',
        '/^Fédération\s/i',
        '/^Parc\s/i',
        '/^Réserve\s/i',
        '/^Syndicat\s/i',
        '/^Office\s/i',
        '/^Conseil\s/i',
        '/^Direction\s/i',
        '/Nature Environnement$/i',
    ];

    public function __construct()
    {
        $this->legacyMembershipType = MembershipType::where('slug', 'legacy-adhesion')->first()
            ?? $this->createLegacyMembershipType();
    }

    protected function createLegacyMembershipType(): MembershipType
    {
        return MembershipType::create([
            'name' => 'Adhésion historique',
            'slug' => 'legacy-adhesion',
            'description' => 'Adhésion de base à 5€ (système avant 2026)',
            'price' => 5.00,
            'duration_months' => 12,
            'is_active' => false,
            'is_legacy' => true,
            'valid_from' => '2008-01-01',
            'valid_until' => '2025-12-31',
            'sort_order' => 100,
        ]);
    }

    public function parseCsvFile(string $path, string $delimiter = ';'): array
    {
        $rows = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier: {$path}");
        }

        // Remove BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headers = fgetcsv($handle, 0, $delimiter);
        if ($headers === false) {
            fclose($handle);
            throw new \RuntimeException("Impossible de lire les en-têtes du fichier CSV");
        }

        // Clean headers
        $headers = array_map(function ($h) {
            return trim(str_replace("\xEF\xBB\xBF", '', $h));
        }, $headers);

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) >= count($headers)) {
                $rows[] = array_combine($headers, array_slice($row, 0, count($headers)));
            }
        }

        fclose($handle);
        return $rows;
    }

    public function mergeOldAndNewFiles(string $oldPath, string $newPath): array
    {
        $oldData = $this->parseCsvFile($oldPath);
        $newData = $this->parseCsvFile($newPath);

        $merged = [];

        // Index old data by normalized name
        $oldByName = [];
        foreach ($oldData as $row) {
            $name = $this->normalizeName($row['Nom et prénom'] ?? '');
            if ($name) {
                $oldByName[$name] = $row;
            }
        }

        // Process new data and merge with old
        foreach ($newData as $row) {
            $name = $this->normalizeName($row['Nom et prénom'] ?? '');
            if (!$name) continue;

            $mergedRow = $row;

            // Merge old year columns if exists
            if (isset($oldByName[$name])) {
                $oldRow = $oldByName[$name];
                foreach ($oldRow as $key => $value) {
                    // Add year columns from old file (2008-2022)
                    if (preg_match('/^20(0[89]|1\d|2[0-2])$/', $key)) {
                        $mergedRow[$key] = $value;
                    }
                }
                unset($oldByName[$name]);
            }

            $merged[] = $mergedRow;
        }

        // Add remaining old entries that are not in new file
        foreach ($oldByName as $row) {
            $merged[] = $row;
        }

        return $merged;
    }

    protected function normalizeName(string $name): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $name)));
    }

    public function detectContactType(string $fullName): string
    {
        foreach ($this->organizationPatterns as $pattern) {
            if (preg_match($pattern, $fullName)) {
                return Member::TYPE_ASSOCIATION;
            }
        }

        return Member::TYPE_INDIVIDUEL;
    }

    public function parseFullName(string $fullName): array
    {
        $fullName = trim($fullName);
        $contactType = $this->detectContactType($fullName);

        if ($contactType !== Member::TYPE_INDIVIDUEL) {
            return [
                'first_name' => null,
                'last_name' => $fullName,
                'civilite' => null,
                'contact_type' => $contactType,
            ];
        }

        // Handle "LASTNAME Firstname" format
        // Also handle cases like "LASTNAME Firstname (Organization)"
        $name = preg_replace('/\s*\([^)]+\)\s*/', '', $fullName);

        $parts = preg_split('/\s+/', trim($name), 2);

        if (count($parts) === 1) {
            return [
                'first_name' => null,
                'last_name' => $parts[0],
                'civilite' => null,
                'contact_type' => $contactType,
            ];
        }

        return [
            'first_name' => $parts[1],
            'last_name' => $parts[0],
            'civilite' => null,
            'contact_type' => $contactType,
        ];
    }

    public function findOrCreateMember(array $row): ?Member
    {
        $fullName = trim($row['Nom et prénom'] ?? '');
        if (empty($fullName)) {
            return null;
        }

        $email = $this->cleanEmail($row['Adresse courriel'] ?? '');
        $parsedName = $this->parseFullName($fullName);

        // Try to find by email first
        if ($email) {
            $member = Member::where('email', $email)->first();
            if ($member) {
                $this->stats['members_updated']++;
                return $member;
            }
        }

        // Try to find by name
        $member = Member::where('last_name', mb_strtoupper($parsedName['last_name']))
            ->where('first_name', $parsedName['first_name'])
            ->first();

        if ($member) {
            // Update email if we have one and member doesn't
            if ($email && !$member->email) {
                $member->update(['email' => $email]);
            }
            $this->stats['members_updated']++;
            return $member;
        }

        // Create new member
        $member = Member::create([
            'member_number' => Member::generateMemberNumber(),
            'contact_type' => $parsedName['contact_type'],
            'civilite' => $parsedName['civilite'],
            'first_name' => $parsedName['first_name'],
            'last_name' => $parsedName['last_name'],
            'email' => $email,
            'phone' => $this->cleanPhone($row['Tél. portables'] ?? $row['Tél. fixes'] ?? ''),
            'telephone_fixe' => $this->cleanPhone($row['Tél. fixes'] ?? ''),
            'mobile' => $this->cleanPhone($row['Tél. portables'] ?? ''),
            'address' => trim($row['Adresse'] ?? ''),
            'postal_code' => trim($row['Code'] ?? ''),
            'city' => trim($row['Localité'] ?? ''),
            'country' => trim($row['Pays'] ?? 'France'),
            'is_active' => true,
        ]);

        $this->stats['members_created']++;
        return $member;
    }

    protected function cleanEmail(?string $email): ?string
    {
        $email = trim($email ?? '');
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    protected function cleanPhone(?string $phone): ?string
    {
        $phone = trim($phone ?? '');
        return $phone ?: null;
    }

    public function determineMembershipType2026(int $amount, bool $isOrganization, bool $isForeign): MembershipType
    {
        if ($isOrganization) {
            return MembershipType::where('slug', 'personne-morale')->first() ?? $this->legacyMembershipType;
        }

        if ($isForeign) {
            return MembershipType::where('slug', 'hors-france')->first() ?? $this->legacyMembershipType;
        }

        return match (true) {
            $amount >= 50 => MembershipType::where('slug', 'bienfaiteur-2026')->first() ?? $this->legacyMembershipType,
            $amount >= 25 => MembershipType::where('slug', 'famille')->first() ?? $this->legacyMembershipType,
            $amount >= 20 => MembershipType::where('slug', 'individuelle-france')->first() ?? $this->legacyMembershipType,
            $amount >= 12 => MembershipType::where('slug', 'petit-budget')->first() ?? $this->legacyMembershipType,
            default => MembershipType::where('slug', 'individuelle-france')->first() ?? $this->legacyMembershipType,
        };
    }

    public function createMembershipForYear(
        Member $member,
        int $year,
        int $totalAmount,
        ?string $paymentMethod = null
    ): ?Membership {
        // Check if membership already exists
        $existing = Membership::where('member_id', $member->id)
            ->whereYear('start_date', $year)
            ->first();

        if ($existing) {
            return $existing;
        }

        $isOrganization = $member->contact_type !== Member::TYPE_INDIVIDUEL;
        $isForeign = $member->country && !in_array(mb_strtolower($member->country), ['france', 'fr', '']);

        // For 2026+, use new membership types with full amount
        if ($year >= 2026) {
            $membershipType = $this->determineMembershipType2026($totalAmount, $isOrganization, $isForeign);
            $membershipAmount = $totalAmount;
        } else {
            // Before 2026: fixed 5€ membership
            $membershipType = $this->legacyMembershipType;
            $membershipAmount = 5.00;
        }

        $membership = Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $membershipType->id,
            'start_date' => "{$year}-01-01",
            'end_date' => "{$year}-12-31",
            'amount_paid' => $membershipAmount,
            'payment_method' => $this->normalizePaymentMethod($paymentMethod),
            'status' => $year < date('Y') ? 'expired' : 'active',
            'import_source' => 'legacy_csv',
            'import_reference' => "import_" . date('Y-m-d'),
        ]);

        $this->stats['memberships_created']++;
        return $membership;
    }

    public function createMagazinePurchase(
        Member $member,
        Membership $membership,
        int $year,
        float $magazineAmount
    ): ?Purchase {
        if ($magazineAmount <= 0) {
            return null;
        }

        // Find or create magazine product for this year
        $product = Product::findOrCreateMagazineForYear($year, $magazineAmount);

        // Check if purchase already exists
        $existing = Purchase::where('member_id', $member->id)
            ->where('product_id', $product->id)
            ->where('legacy_membership_id', $membership->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $purchase = Purchase::create([
            'member_id' => $member->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $magazineAmount,
            'total_amount' => $magazineAmount,
            'purchase_date' => "{$year}-01-01",
            'payment_method' => $membership->payment_method,
            'source' => Purchase::SOURCE_IMPORT,
            'legacy_membership_id' => $membership->id,
            'notes' => "Import depuis fichier adhérents historique",
        ]);

        $this->stats['purchases_created']++;
        return $purchase;
    }

    protected function normalizePaymentMethod(?string $method): ?string
    {
        if (!$method) {
            return null;
        }

        $method = mb_strtolower(trim($method));

        return match (true) {
            str_contains($method, 'helloasso') => 'helloasso',
            str_contains($method, 'chèque') || str_contains($method, 'cheque') => 'cheque',
            str_contains($method, 'virement') => 'virement',
            str_contains($method, 'espèce') || str_contains($method, 'espece') => 'especes',
            str_contains($method, 'cb') || str_contains($method, 'carte') => 'carte',
            default => $method,
        };
    }

    public function importRow(array $row): bool
    {
        $this->stats['rows_processed']++;

        try {
            $member = $this->findOrCreateMember($row);
            if (!$member) {
                $this->stats['rows_skipped']++;
                return false;
            }

            // Get payment method for 2026 (column "2026 modalités")
            $paymentMethod2026 = $row['2026 modalités'] ?? null;

            // Process each year column
            $years = range(2008, 2026);
            foreach ($years as $year) {
                $yearKey = (string) $year;
                if (!isset($row[$yearKey])) {
                    continue;
                }

                $value = trim($row[$yearKey]);

                // Skip empty, "S", or non-numeric values
                if (empty($value) || $value === 'S' || !is_numeric($value)) {
                    continue;
                }

                $totalAmount = (int) $value;
                if ($totalAmount <= 0) {
                    continue;
                }

                // Determine payment method
                $paymentMethod = ($year === 2026) ? $paymentMethod2026 : null;

                // Create membership
                $membership = $this->createMembershipForYear($member, $year, $totalAmount, $paymentMethod);

                if (!$membership) {
                    continue;
                }

                // For years before 2026: create magazine purchase with remaining amount
                if ($year < 2026) {
                    $magazineAmount = $totalAmount - 5; // Total - 5€ adhesion
                    if ($magazineAmount > 0) {
                        $this->createMagazinePurchase($member, $membership, $year, $magazineAmount);
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $row['Nom et prénom'] ?? 'Unknown',
                'error' => $e->getMessage(),
            ];
            Log::error("Import error for row", [
                'row' => $row,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    public function runImport(string $oldFilePath, string $newFilePath, bool $dryRun = false): array
    {
        $this->errors = [];
        $this->stats = [
            'members_created' => 0,
            'members_updated' => 0,
            'memberships_created' => 0,
            'purchases_created' => 0,
            'rows_skipped' => 0,
            'rows_processed' => 0,
        ];

        $mergedData = $this->mergeOldAndNewFiles($oldFilePath, $newFilePath);

        if ($dryRun) {
            // In dry run, just analyze without creating
            foreach ($mergedData as $row) {
                $this->analyzeRow($row);
            }
        } else {
            DB::beginTransaction();
            try {
                foreach ($mergedData as $row) {
                    $this->importRow($row);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return [
            'stats' => $this->stats,
            'errors' => $this->errors,
            'total_rows' => count($mergedData),
        ];
    }

    protected function analyzeRow(array $row): void
    {
        $this->stats['rows_processed']++;

        $fullName = trim($row['Nom et prénom'] ?? '');
        if (empty($fullName)) {
            $this->stats['rows_skipped']++;
            return;
        }

        $email = $this->cleanEmail($row['Adresse courriel'] ?? '');

        // Check if would be update or create
        $existingMember = null;
        if ($email) {
            $existingMember = Member::where('email', $email)->first();
        }

        if (!$existingMember) {
            $parsedName = $this->parseFullName($fullName);
            $existingMember = Member::where('last_name', mb_strtoupper($parsedName['last_name']))
                ->where('first_name', $parsedName['first_name'])
                ->first();
        }

        if ($existingMember) {
            $this->stats['members_updated']++;
        } else {
            $this->stats['members_created']++;
        }

        // Count memberships that would be created
        $years = range(2008, 2026);
        foreach ($years as $year) {
            $yearKey = (string) $year;
            if (!isset($row[$yearKey])) continue;

            $value = trim($row[$yearKey]);
            if (empty($value) || $value === 'S' || !is_numeric($value)) continue;

            $totalAmount = (int) $value;
            if ($totalAmount <= 0) continue;

            $this->stats['memberships_created']++;

            if ($year < 2026 && ($totalAmount - 5) > 0) {
                $this->stats['purchases_created']++;
            }
        }
    }

    public function getStats(): array
    {
        return $this->stats;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
