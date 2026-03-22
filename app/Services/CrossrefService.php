<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\JournalIssue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CrossrefService
{
    protected string $depositUrl;
    protected string $username;
    protected string $password;
    protected string $doiPrefix;
    protected string $registrant;

    public function __construct()
    {
        $this->depositUrl = config('services.crossref.deposit_url', 'https://doi.crossref.org/servlet/deposit');
        $this->username = config('services.crossref.username', '');
        $this->password = config('services.crossref.password', '');
        $this->doiPrefix = config('services.crossref.doi_prefix', '10.24349');
        $this->registrant = config('services.crossref.registrant', 'OREINA');
    }

    /**
     * Check if Crossref is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->username) && !empty($this->password);
    }

    /**
     * Generate a DOI suffix for a submission
     */
    public function generateDoiSuffix(Submission $submission): string
    {
        // Generate a unique suffix based on submission ID and random string
        $random = strtolower(Str::random(4));
        return "{$random}-{$submission->id}";
    }

    /**
     * Get the full DOI for a submission
     */
    public function getFullDoi(Submission $submission): string
    {
        if ($submission->doi) {
            return $submission->doi;
        }

        return "{$this->doiPrefix}/{$this->generateDoiSuffix($submission)}";
    }

    /**
     * Register a DOI with Crossref
     */
    public function registerDoi(Submission $submission): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Crossref non configuré. Veuillez ajouter les identifiants dans la configuration.',
                'doi' => null,
            ];
        }

        // Generate DOI if not already set
        $doi = $submission->doi ?: $this->getFullDoi($submission);

        // Build XML deposit
        $xml = $this->buildDepositXml($submission, $doi);

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->attach('mdFile', $xml, 'deposit.xml')
                ->post($this->depositUrl, [
                    'operation' => 'doMDUpload',
                    'login_id' => $this->username,
                    'login_passwd' => $this->password,
                ]);

            if ($response->successful()) {
                // Update submission with DOI
                $submission->update(['doi' => $doi]);

                Log::info("DOI registered successfully: {$doi}", [
                    'submission_id' => $submission->id,
                    'response' => $response->body(),
                ]);

                return [
                    'success' => true,
                    'doi' => $doi,
                    'message' => "DOI {$doi} enregistré avec succès.",
                ];
            } else {
                Log::error("Crossref DOI registration failed", [
                    'submission_id' => $submission->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => "Erreur lors de l'enregistrement: " . $response->body(),
                    'doi' => null,
                ];
            }
        } catch (\Exception $e) {
            Log::error("Crossref DOI registration exception", [
                'submission_id' => $submission->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => "Exception: " . $e->getMessage(),
                'doi' => null,
            ];
        }
    }

    /**
     * Build the Crossref deposit XML
     */
    protected function buildDepositXml(Submission $submission, string $doi): string
    {
        $submission->load(['author', 'journalIssue']);

        $batchId = 'oreina-' . time() . '-' . $submission->id;
        $timestamp = now()->format('YmdHis');

        $journalTitle = config('journal.name', 'Chersotis');
        $journalAbbrev = config('journal.abbreviation', 'Chersotis');
        $issnPrint = config('journal.issn_print', '');
        $issnElectronic = config('journal.issn_electronic', '');

        $issue = $submission->journalIssue;
        $volumeNumber = $issue?->volume_number ?? '1';
        $issueNumber = $issue?->issue_number ?? '1';
        $publicationYear = $submission->published_at?->format('Y') ?? now()->format('Y');
        $publicationMonth = $submission->published_at?->format('m') ?? now()->format('m');
        $publicationDay = $submission->published_at?->format('d') ?? now()->format('d');

        // Build authors XML
        $authorsXml = $this->buildAuthorsXml($submission);

        // Article URL
        $articleUrl = route('journal.articles.show', $submission);

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<doi_batch xmlns="http://www.crossref.org/schema/5.3.1"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xmlns:jats="http://www.ncbi.nlm.nih.gov/JATS1"
           xmlns:ai="http://www.crossref.org/AccessIndicators.xsd"
           xsi:schemaLocation="http://www.crossref.org/schema/5.3.1 https://www.crossref.org/schemas/crossref5.3.1.xsd"
           version="5.3.1">
    <head>
        <doi_batch_id>{$batchId}</doi_batch_id>
        <timestamp>{$timestamp}</timestamp>
        <depositor>
            <depositor_name>{$this->registrant}</depositor_name>
            <email_address>contact@oreina.org</email_address>
        </depositor>
        <registrant>{$this->registrant}</registrant>
    </head>
    <body>
        <journal>
            <journal_metadata language="fr">
                <full_title>{$journalTitle}</full_title>
                <abbrev_title>{$journalAbbrev}</abbrev_title>
XML;

        if ($issnPrint) {
            $xml .= <<<XML

                <issn media_type="print">{$issnPrint}</issn>
XML;
        }

        if ($issnElectronic) {
            $xml .= <<<XML

                <issn media_type="electronic">{$issnElectronic}</issn>
XML;
        }

        $xml .= <<<XML

            </journal_metadata>
            <journal_issue>
                <publication_date media_type="online">
                    <month>{$publicationMonth}</month>
                    <day>{$publicationDay}</day>
                    <year>{$publicationYear}</year>
                </publication_date>
                <journal_volume>
                    <volume>{$volumeNumber}</volume>
                </journal_volume>
                <issue>{$issueNumber}</issue>
            </journal_issue>
            <journal_article publication_type="full_text">
                <titles>
                    <title>{$this->escapeXml($submission->title)}</title>
                </titles>
                <contributors>
{$authorsXml}
                </contributors>
                <publication_date media_type="online">
                    <month>{$publicationMonth}</month>
                    <day>{$publicationDay}</day>
                    <year>{$publicationYear}</year>
                </publication_date>
XML;

        if ($submission->start_page && $submission->end_page) {
            $xml .= <<<XML

                <pages>
                    <first_page>{$submission->start_page}</first_page>
                    <last_page>{$submission->end_page}</last_page>
                </pages>
XML;
        }

        $xml .= <<<XML

                <ai:program name="AccessIndicators">
                    <ai:license_ref applies_to="vor">https://creativecommons.org/licenses/by/4.0/</ai:license_ref>
                </ai:program>
                <doi_data>
                    <doi>{$doi}</doi>
                    <resource>{$articleUrl}</resource>
                </doi_data>
            </journal_article>
        </journal>
    </body>
</doi_batch>
XML;

        return $xml;
    }

    /**
     * Build authors XML section
     */
    protected function buildAuthorsXml(Submission $submission): string
    {
        $xml = '';
        $sequence = 'first';

        // Main author
        if ($submission->author) {
            $names = $this->parseAuthorName($submission->author->name);
            $xml .= $this->buildPersonXml($names['given'], $names['surname'], $sequence, $submission->author->orcid ?? null);
            $sequence = 'additional';
        }

        // Co-authors
        if ($submission->co_authors && is_array($submission->co_authors)) {
            foreach ($submission->co_authors as $coAuthor) {
                if (!empty($coAuthor['name'])) {
                    $names = $this->parseAuthorName($coAuthor['name']);
                    $orcid = $coAuthor['orcid'] ?? null;
                    $xml .= $this->buildPersonXml($names['given'], $names['surname'], $sequence, $orcid);
                }
            }
        }

        return $xml;
    }

    /**
     * Build person_name XML element
     */
    protected function buildPersonXml(string $given, string $surname, string $sequence, ?string $orcid = null): string
    {
        $xml = <<<XML
                    <person_name sequence="{$sequence}" contributor_role="author">
                        <given_name>{$this->escapeXml($given)}</given_name>
                        <surname>{$this->escapeXml($surname)}</surname>
XML;

        if ($orcid) {
            $xml .= <<<XML

                        <ORCID>https://orcid.org/{$orcid}</ORCID>
XML;
        }

        $xml .= <<<XML

                    </person_name>

XML;

        return $xml;
    }

    /**
     * Parse author name into given and surname
     */
    protected function parseAuthorName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName));

        if (count($parts) === 1) {
            return ['given' => '', 'surname' => $parts[0]];
        }

        $surname = array_pop($parts);
        $given = implode(' ', $parts);

        return ['given' => $given, 'surname' => $surname];
    }

    /**
     * Escape special XML characters
     */
    protected function escapeXml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Assign a DOI locally without registering with Crossref
     * Useful for testing or when Crossref is not configured
     */
    public function assignLocalDoi(Submission $submission): string
    {
        $doi = $this->getFullDoi($submission);
        $submission->update(['doi' => $doi]);
        return $doi;
    }

    /**
     * Validate a DOI format
     */
    public function validateDoi(string $doi): bool
    {
        // DOI format: 10.XXXX/suffix
        return (bool) preg_match('/^10\.\d{4,}\/[^\s]+$/', $doi);
    }

    /**
     * Get DOI resolution URL
     */
    public function getDoiUrl(string $doi): string
    {
        return "https://doi.org/{$doi}";
    }
}
