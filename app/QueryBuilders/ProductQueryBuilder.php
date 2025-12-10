<?php

namespace App\QueryBuilders;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductQueryBuilder
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = Product::query()
            ->where('is_active', true)
            ->with(['nomenclature.unit', 'nomenclature.category', 'shop']);
    }

    public static function make(): self
    {
        return new self;
    }

    /**
     * Apply filters from request
     */
    public function applyFilters(Request $request): self
    {
        // Search by product name
        if ($search = $request->input('search')) {
            $this->query->where(function ($q) use ($search) {
                $q->where('name_ru', 'like', "%{$search}%")
                    ->orWhere('name_kz', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $this->query->whereHas('nomenclature', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Filter by nomenclature
        if ($nomenclatureId = $request->input('nomenclature_id')) {
            $this->query->where('nomenclature_id', $nomenclatureId);
        }

        // Filter by shop
        if ($shopId = $request->input('shop_id')) {
            $this->query->where('shop_id', $shopId);
        }

        // Filter by price range
        if ($minPrice = $request->input('min_price')) {
            $this->query->where('price', '>=', $minPrice);
        }

        if ($maxPrice = $request->input('max_price')) {
            $this->query->where('price', '<=', $maxPrice);
        }

        // Filter by stock availability
        if ($request->boolean('in_stock')) {
            $this->query->where('quantity', '>', 0);
        }

        // Filter by specs
        if ($specs = $request->input('specs')) {
            foreach ($specs as $specKey => $specValues) {
                if (! empty($specValues)) {
                    $this->query->whereHas('specs', function ($q) use ($specKey, $specValues) {
                        $q->where('spec_key', $specKey)
                            ->whereIn('spec_value', (array) $specValues);
                    });
                }
            }
        }

        return $this;
    }

    /**
     * Apply sorting
     */
    public function applySort(Request $request): self
    {
        $sortBy = $request->input('sort_by', 'latest');

        match ($sortBy) {
            'price_asc' => $this->query->orderBy('price', 'asc'),
            'price_desc' => $this->query->orderBy('price', 'desc'),
            'popular' => $this->query->orderBy('id', 'desc'), // TODO: Add popularity metric
            'latest' => $this->query->latest(),
            'relevance' => $this->query->latest(), // For search results
            default => $this->query->latest(),
        };

        return $this;
    }

    /**
     * Apply search query (full-text search)
     */
    public function applySearch(string $query): self
    {
        if (empty($query)) {
            return $this;
        }

        $this->query->where(function ($q) use ($query) {
            $q->where('name_ru', 'like', "%{$query}%")
                ->orWhere('name_kz', 'like', "%{$query}%")
                ->orWhereHas('nomenclature', function ($nq) use ($query) {
                    $nq->where('name_ru', 'like', "%{$query}%")
                        ->orWhere('name_kz', 'like', "%{$query}%");
                });
        });

        return $this;
    }

    /**
     * Get the query builder
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Execute and paginate results
     */
    public function paginate(int $perPage = 24)
    {
        return $this->query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filters array from request
     */
    public static function getFiltersFromRequest(Request $request): array
    {
        return [
            'search' => $request->input('search'),
            'category_id' => $request->input('category_id'),
            'nomenclature_id' => $request->input('nomenclature_id'),
            'shop_id' => $request->input('shop_id'),
            'min_price' => $request->input('min_price') ? (float) $request->input('min_price') : null,
            'max_price' => $request->input('max_price') ? (float) $request->input('max_price') : null,
            'in_stock' => $request->boolean('in_stock'),
            'specs' => $request->input('specs', []),
            'sort_by' => $request->input('sort_by', 'latest'),
        ];
    }
}
