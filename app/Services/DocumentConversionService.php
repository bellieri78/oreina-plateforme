<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\ListItemRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Title;
use PhpOffice\PhpWord\IOFactory;

/**
 * Converts .docx / .odt files to Markdown via Claude Haiku API.
 *
 * Pipeline: extractText() → Claude API → Markdown string
 */
class DocumentConversionService
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-haiku-4-5-20251001';
    private const MAX_TOKENS = 16384;
    private const TIMEOUT = 120;

    private const SYSTEM_PROMPT = <<<'PROMPT'
Tu es un convertisseur de documents scientifiques. Convertis le texte suivant en Markdown structuré.
Règles :
- Utilise # / ## / ### pour les titres (déduis le niveau du contexte et de la mise en forme)
- Conserve **gras** et *italique*
- Les noms d'espèces (noms latins binomiaux) doivent être en *italique*
- Utilise des tableaux pipe Markdown pour les tableaux
- Utilise des listes à puces ou numérotées selon le contexte
- Utilise > pour les citations
- Ne modifie pas le contenu textuel, ne résume pas, ne commente pas
- Retourne uniquement le Markdown, sans explication ni balise de code
PROMPT;

    private const STRUCTURED_SYSTEM_PROMPT = <<<'PROMPT'
Tu es un convertisseur de documents scientifiques entomologiques.
Analyse le texte suivant et retourne un JSON structuré avec ces champs :

- "title" : le titre principal de l'article (sans les auteurs)
- "markdown" : le corps de l'article converti en Markdown structuré (## pour les sous-titres, **gras**, *italique*, tableaux pipe, listes). NE PAS inclure : le titre principal, les affiliations des auteurs, les références bibliographiques, les remerciements.
- "references" : tableau JSON de strings, une référence par entrée, dans l'ordre d'apparition. Chaque référence doit être formatée en style Harvard : Auteur(s) (année). Titre. *Revue*, volume(numéro), pages. Si la référence est un livre : Auteur(s) (année). *Titre*. Éditeur, Lieu, pages. Reformater si nécessaire.
- "authors_affiliations" : tableau JSON de strings, un auteur par entrée, au format "Prénom NOM : affiliation complète, email"
- "acknowledgements" : texte des remerciements (chaîne vide si absent)
- "taxons" : tableau JSON des noms d'espèces (noms latins binomiaux) trouvés dans le texte, sans doublons

Règles pour le Markdown :
- Les noms d'espèces doivent être en *italique*
- Conserver le formatage (gras, italique, exposant, indice)
- Utiliser des tableaux pipe pour les tableaux
- Ne pas modifier le contenu textuel, ne pas résumer
- Retourner uniquement le JSON, sans explication ni balise de code
PROMPT;

    /**
     * Full pipeline: extract text from document → send to Claude → return Markdown.
     *
     * @throws \RuntimeException
     */
    public function toMarkdown(string $filePath): string
    {
        $text = $this->extractText($filePath);

        if (trim($text) === '') {
            throw new \RuntimeException(
                'Le document ne contient pas de texte exploitable.'
            );
        }

        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'La clé API Anthropic n\'est pas configurée (services.anthropic.api_key).'
            );
        }

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])
        ->timeout(self::TIMEOUT)
        ->post(self::API_URL, [
            'model'      => self::MODEL,
            'max_tokens' => self::MAX_TOKENS,
            'system'     => self::SYSTEM_PROMPT,
            'messages'   => [
                [
                    'role'    => 'user',
                    'content' => $text,
                ],
            ],
        ]);

        if ($response->failed()) {
            $errorMessage = $response->json('error.message') ?? $response->body();
            throw new \RuntimeException(
                'Erreur de l\'API Anthropic : ' . $errorMessage
            );
        }

        $markdown = $response->json('content.0.text');

        if (empty($markdown)) {
            throw new \RuntimeException(
                'L\'API Anthropic n\'a retourné aucun contenu.'
            );
        }

        return $markdown;
    }

    /**
     * Full pipeline: extract text from document → send to Claude → return structured array.
     *
     * Returns an array with keys: title, markdown, references, authors_affiliations,
     * acknowledgements, taxons. Falls back gracefully if Claude returns non-JSON.
     *
     * @throws \RuntimeException
     */
    public function toStructured(string $filePath): array
    {
        $text = $this->extractText($filePath);

        if (trim($text) === '') {
            throw new \RuntimeException(
                'Le document ne contient pas de texte exploitable.'
            );
        }

        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'La clé API Anthropic n\'est pas configurée (services.anthropic.api_key).'
            );
        }

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])
        ->timeout(self::TIMEOUT)
        ->post(self::API_URL, [
            'model'      => self::MODEL,
            'max_tokens' => self::MAX_TOKENS,
            'system'     => self::STRUCTURED_SYSTEM_PROMPT,
            'messages'   => [
                [
                    'role'    => 'user',
                    'content' => $text,
                ],
            ],
        ]);

        if ($response->failed()) {
            $errorMessage = $response->json('error.message') ?? $response->body();
            throw new \RuntimeException(
                'Erreur de l\'API Anthropic : ' . $errorMessage
            );
        }

        $content = $response->json('content.0.text');

        if (empty($content)) {
            throw new \RuntimeException(
                'L\'API Anthropic n\'a retourné aucun contenu.'
            );
        }

        return $this->parseStructuredResponse($content);
    }

    /**
     * Extract plain text from a .docx or .odt file using PhpWord.
     *
     * @throws \RuntimeException
     */
    public function extractText(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $readerName = $extension === 'odt' ? 'ODText' : 'Word2007';

        try {
            $reader = IOFactory::createReader($readerName);
            if (method_exists($reader, 'setImageLoading')) {
                $reader->setImageLoading(false);
            }
            $phpWord = $reader->load($filePath);
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'Impossible de lire le document : ' . $e->getMessage()
            );
        }

        $parts = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text = $this->extractElementText($element);
                if ($text !== '') {
                    $parts[] = $text;
                }
            }
        }

        return implode("\n\n", $parts);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Extract text from a single PhpWord element.
     */
    private function extractElementText(mixed $element): string
    {
        if ($element instanceof Title) {
            $text = $element->getText();
            if ($text instanceof TextRun) {
                return $this->extractTextRunPlain($text);
            }
            return is_string($text) ? trim($text) : '';
        }

        if ($element instanceof Table) {
            return $this->extractTableText($element);
        }

        if ($element instanceof ListItem) {
            $text = (string) ($element->getText() ?? '');
            return trim($text) !== '' ? '- ' . trim($text) : '';
        }

        if ($element instanceof ListItemRun) {
            $text = $this->extractListItemRunText($element);
            return $text !== '' ? '- ' . $text : '';
        }

        if ($element instanceof TextRun) {
            return $this->extractTextRunPlain($element);
        }

        if ($element instanceof Text) {
            return trim((string) ($element->getText() ?? ''));
        }

        return '';
    }

    /**
     * Extract plain text from a TextRun element.
     */
    private function extractTextRunPlain(TextRun $textRun): string
    {
        $parts = [];
        foreach ($textRun->getElements() as $child) {
            if ($child instanceof Text) {
                $t = trim((string) ($child->getText() ?? ''));
                if ($t !== '') {
                    $parts[] = $t;
                }
            }
        }
        return implode(' ', $parts);
    }

    /**
     * Extract plain text from a ListItemRun element.
     */
    private function extractListItemRunText(ListItemRun $element): string
    {
        $parts = [];
        foreach ($element->getElements() as $child) {
            if ($child instanceof Text) {
                $t = trim((string) ($child->getText() ?? ''));
                if ($t !== '') {
                    $parts[] = $t;
                }
            }
        }
        return implode(' ', $parts);
    }

    /**
     * Parse a Claude response string as structured JSON.
     *
     * Strips optional markdown code fences (```json ... ```) before decoding.
     * Falls back to a default array with the raw content as markdown if JSON
     * parsing fails.
     */
    private function parseStructuredResponse(string $content): array
    {
        $fallback = [
            'title'               => '',
            'markdown'            => $content,
            'references'          => [],
            'authors_affiliations' => [],
            'acknowledgements'    => '',
            'taxons'              => [],
        ];

        // Strip markdown code fences if present
        $stripped = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
        $stripped = preg_replace('/\s*```$/', '', $stripped ?? $content);

        $decoded = json_decode($stripped ?? $content, true);

        if (!is_array($decoded)) {
            return $fallback;
        }

        return [
            'title'               => $decoded['title'] ?? '',
            'markdown'            => $decoded['markdown'] ?? '',
            'references'          => $this->flattenToStrings($decoded['references'] ?? []),
            'authors_affiliations' => $this->flattenToStrings($decoded['authors_affiliations'] ?? []),
            'acknowledgements'    => $decoded['acknowledgements'] ?? '',
            'taxons'              => $this->flattenToStrings($decoded['taxons'] ?? []),
        ];
    }

    /**
     * Flatten an array of mixed items (strings or nested arrays) into an array of strings.
     */
    private function flattenToStrings(array $items): array
    {
        return array_map(function ($item) {
            if (is_string($item)) {
                return $item;
            }
            if (is_array($item)) {
                return $this->flattenValue($item);
            }
            return (string) $item;
        }, $items);
    }

    private function flattenValue(array $item): string
    {
        $parts = [];
        foreach ($item as $value) {
            if (is_array($value)) {
                $parts[] = $this->flattenValue($value);
            } elseif (is_string($value) || is_numeric($value)) {
                $parts[] = (string) $value;
            }
        }
        return implode(', ', array_filter($parts));
    }

    /**
     * Extract plain text from a Table element, each row as "cell1 | cell2".
     */
    private function extractTableText(Table $table): string
    {
        $rows = [];
        foreach ($table->getRows() as $row) {
            $cells = [];
            foreach ($row->getCells() as $cell) {
                $cellParts = [];
                foreach ($cell->getElements() as $element) {
                    if ($element instanceof Text) {
                        $t = trim((string) ($element->getText() ?? ''));
                        if ($t !== '') {
                            $cellParts[] = $t;
                        }
                    } elseif ($element instanceof TextRun) {
                        $t = $this->extractTextRunPlain($element);
                        if ($t !== '') {
                            $cellParts[] = $t;
                        }
                    }
                }
                $cells[] = implode(' ', $cellParts);
            }
            if (!empty(array_filter($cells))) {
                $rows[] = implode(' | ', $cells);
            }
        }
        return implode("\n", $rows);
    }
}
