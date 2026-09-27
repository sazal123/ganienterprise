<?php

namespace App\Services\AI\Tools;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductSearchTool
{
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
                'name'        => 'product_search',
                'description' => 'Search the store product catalog by name, category, or keyword, or retrieve available products. Use query "all" or leave empty to list top available store products when customer asks generally what products are in stock.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'query' => [
                            'type'        => 'string',
                            'description' => 'The search term (e.g. product name, category, or "all" to list available catalog).',
                        ],
                        'limit' => [
                            'type'        => 'integer',
                            'description' => 'Maximum number of results to return (default 5, max 10).',
                        ],
                    ],
                    'required'   => ['query'],
                ],
            ],
        ];
    }

    /**
     * Execute product search against database with strict public field whitelisting.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function search(string $query, int $limit = 5): array
    {
        // 1. Sanitize and clean search query string
        $cleanQuery = trim(strip_tags($query));
        $cleanQuery = Str::limit($cleanQuery, 100, '');
        $lowerQuery = strtolower($cleanQuery);

        $safeLimit = max(1, min((int) $limit, 10));

        $genericKeywords = ['all', 'list', 'product', 'products', 'catalog', 'available', 'show', 'browse', 'item', 'items', 'ki ki', 'ace', 'panno', 'ponno', 'sob', 'anything', 'store', 'stock'];
        $isGenericSearch = empty($cleanQuery)
            || in_array($lowerQuery, $genericKeywords)
            || str_contains($lowerQuery, 'ki ki')
            || str_contains($lowerQuery, 'what product')
            || str_contains($lowerQuery, 'all product')
            || str_contains($lowerQuery, 'show product');

        $cacheKey = 'product_search_' . md5(($isGenericSearch ? 'GENERIC_ALL' : $cleanQuery) . '_' . $safeLimit);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function () use ($cleanQuery, $isGenericSearch, $safeLimit) {
            // 2. Query products securely using Eloquent parameter bindings
            $queryBuilder = Product::with(['category:id,name', 'brand:id,name', 'sizes', 'colors'])
                ->where('status', 1);

            if (!$isGenericSearch) {
                $queryBuilder->where(function ($q) use ($cleanQuery) {
                    $q->where('name', 'LIKE', "%{$cleanQuery}%")
                      ->orWhere('product_code', 'LIKE', "%{$cleanQuery}%")
                      ->orWhere('description', 'LIKE', "%{$cleanQuery}%")
                      ->orWhereHas('category', function ($catQ) use ($cleanQuery) {
                          $catQ->where('name', 'LIKE', "%{$cleanQuery}%");
                      })
                      ->orWhereHas('brand', function ($brandQ) use ($cleanQuery) {
                          $brandQ->where('name', 'LIKE', "%{$cleanQuery}%");
                      });
                });
            } else {
                $queryBuilder->orderBy('topsale', 'desc')
                    ->orderBy('feature_product', 'desc')
                    ->orderBy('id', 'desc');
            }

            $products = $queryBuilder->limit($safeLimit)->get();

            if ($products->isEmpty()) {
                return [
                    'found'   => false,
                    'count'   => 0,
                    'results' => [],
                    'message' => $isGenericSearch ? "No active products currently available in store." : "No products found matching '{$cleanQuery}'.",
                ];
            }

            // 3. Map strictly WHITELISTED public attributes (Never leak purchase_price, margin, admin fields)
            $publicResults = $products->map(function (Product $product) {
                $inStock = (int) $product->stock > 0;

                return [
                    'name'             => (string) $product->name,
                    'product_code'     => (string) $product->product_code,
                    'category'         => $product->category ? $product->category->name : null,
                    'brand'            => $product->brand ? $product->brand->name : null,
                    'price'            => (float) $product->new_price,
                    'old_price'        => $product->old_price ? (float) $product->old_price : null,
                    'availability'     => $inStock ? 'In Stock' : 'Out of Stock',
                    'stock_quantity'   => (int) $product->stock,
                    'description'      => Str::limit(strip_tags($product->description ?? $product->meta_description ?? ''), 300),
                ];
            })->values()->toArray();

            return [
                'found'   => true,
                'count'   => count($publicResults),
                'results' => $publicResults,
            ];
        });
    }
}
