<?php

namespace App\Services\AI\Tools;

use App\Models\KnowledgeArticle;
use Illuminate\Support\Str;

class KnowledgeSearchTool
{
    public const UNVERIFIED_FALLBACK_MESSAGE = "I don't have verified information about that. Would you like me to connect you with our support team?";

    /**
     * Get OpenRouter / OpenAI compatible tool definition schema.
     *
     * @return array
     */
    public static function getDefinition(): array
    {
        return [
            'type'     => 'function',
            'function' => [
                'name'        => 'knowledge_search',
                'description' => 'Search official company policy and FAQ articles (returns, refunds, shipping, cancellation, payment, general info). Use this tool whenever a customer asks policy or store rule questions.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'query' => [
                            'type'        => 'string',
                            'description' => 'The policy topic, question, or keyword to search for (e.g. return policy, refund timeframe, shipping fees, cancellation).',
                        ],
                        'category' => [
                            'type'        => 'string',
                            'description' => 'Optional policy category (faq, shipping, returns, refunds, cancellation, payment, general).',
                        ],
                    ],
                    'required'   => ['query'],
                ],
            ],
        ];
    }

    /**
     * Execute search against published knowledge articles in database.
     *
     * @param string $query
     * @param string|null $category
     * @param int $limit
     * @return array
     */
    public function search(string $query, ?string $category = null, int $limit = 3): array
    {
        $cleanQuery = trim(strip_tags($query));
        $cleanQuery = Str::limit($cleanQuery, 100, '');

        if (empty($cleanQuery)) {
            return [
                'found'   => false,
                'count'   => 0,
                'results' => [],
                'message' => self::UNVERIFIED_FALLBACK_MESSAGE,
            ];
        }

        $safeLimit = max(1, min((int) $limit, 10));

        $cacheKey = 'knowledge_search_' . md5($cleanQuery . '_' . strtolower((string)$category) . '_' . $safeLimit);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function () use ($cleanQuery, $category, $safeLimit) {
            // Query ONLY published articles
            $queryBuilder = KnowledgeArticle::where(function ($q) {
                    $q->where('status', 'published')
                      ->orWhere('status', '1');
                })
                ->where(function ($q) use ($cleanQuery) {
                    $q->where('title', 'LIKE', "%{$cleanQuery}%")
                      ->orWhere('content', 'LIKE', "%{$cleanQuery}%")
                      ->orWhere('category', 'LIKE', "%{$cleanQuery}%");
                });

            if (!empty($category)) {
                $cleanCat = strtolower(trim($category));
                $queryBuilder->where('category', 'LIKE', "%{$cleanCat}%");
            }

            $articles = $queryBuilder->limit($safeLimit)->get();

            if ($articles->isEmpty()) {
                return [
                    'found'   => false,
                    'count'   => 0,
                    'results' => [],
                    'message' => self::UNVERIFIED_FALLBACK_MESSAGE,
                ];
            }

            $publicArticles = $articles->map(function (KnowledgeArticle $article) {
                return [
                    'title'    => (string) $article->title,
                    'category' => (string) ($article->category ?? 'general'),
                    'content'  => strip_tags((string) $article->content),
                ];
            })->values()->toArray();

            return [
                'found'   => true,
                'count'   => count($publicArticles),
                'results' => $publicArticles,
            ];
        });
    }
}
