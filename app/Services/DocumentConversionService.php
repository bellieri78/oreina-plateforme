<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Enriches Markdown content via Claude Haiku API.
 *
 * Extracts structured metadata (title, references, affiliations,
 * acknowledgements, taxons) and returns clean Markdown body.
 */
class DocumentConversionService
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-haiku-4-5-20251001';
    private const MAX_TOKENS = 16384;
    private const TIMEOUT = 180;

    private const SYSTEM_PROMPT = <<<'PROMPT'
Tu es un convertisseur de documents scientifiques entomologiques.
Analyse le Markdown suivant et retourne un JSON structuré avec ces champs :

- "title" : le titre principal de l'article (sans les auteurs)
- "markdown" : le corps de l'article en Markdown structuré (## pour les sous-titres, **gras**, *italique*, tableaux pipe, listes). NE PAS inclure : le titre principal, les affiliations des auteurs en début d'article, la section "Références bibliographiques" / "Bibliographie" de fin d'article, la section "Remerciements". IMPORTANT : conserver les citations inline dans le texte telles quelles, par exemple (Dupont, 2023) ou (Opie & CEN Occitanie (coord.), 2022) — ce sont des renvois, pas des références à supprimer.
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
     * Enrich Markdown content via Claude API.
     *
     * @return array{title: string, markdown: string, references: string[], authors_affiliations: string[], acknowledgements: string, taxons: string[]}
     * @throws \RuntimeException
     */
    public function enrichMarkdown(string $markdown): array
    {
        if (trim($markdown) === '') {
            throw new \RuntimeException(
                'Le document ne contient pas de texte exploitable.'
            );
        }

        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'La clé API Anthropic n\'est pas configurée.'
            );
        }

        \Log::info('DocumentConversion: enriching markdown', [
            'chars' => strlen($markdown),
            'words' => str_word_count($markdown),
        ]);

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
                ['role' => 'user', 'content' => $markdown],
            ],
        ]);

        if ($response->failed()) {
            $errorMessage = $response->json('error.message') ?? $response->body();
            throw new \RuntimeException(
                'Erreur de l\'API Anthropic : ' . $errorMessage
            );
        }

        $content = $response->json('content.0.text', '');

        if (empty($content)) {
            throw new \RuntimeException(
                'L\'API Anthropic n\'a retourné aucun contenu.'
            );
        }

        return $this->parseStructuredResponse($content);
    }

    private function parseStructuredResponse(string $content): array
    {
        $fallback = [
            'title'                => '',
            'markdown'             => $content,
            'references'           => [],
            'authors_affiliations' => [],
            'acknowledgements'     => '',
            'taxons'               => [],
        ];

        $stripped = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
        $stripped = preg_replace('/\s*```$/', '', $stripped ?? $content);

        $decoded = json_decode($stripped ?? $content, true);

        if (!is_array($decoded)) {
            return $fallback;
        }

        return [
            'title'                => $decoded['title'] ?? '',
            'markdown'             => $decoded['markdown'] ?? '',
            'references'           => $this->flattenToStrings($decoded['references'] ?? []),
            'authors_affiliations' => $this->flattenToStrings($decoded['authors_affiliations'] ?? []),
            'acknowledgements'     => $decoded['acknowledgements'] ?? '',
            'taxons'               => $this->flattenToStrings($decoded['taxons'] ?? []),
        ];
    }

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
}
