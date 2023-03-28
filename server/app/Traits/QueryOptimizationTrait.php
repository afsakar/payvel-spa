<?php

namespace App\Traits;

use App\Traits\Cacheable;
use Illuminate\Support\Facades\Cache;

/**
 * TODO: Refactor this trait later
 */

trait QueryOptimizationTrait
{
    use Cacheable;

    public function optimizeQuery($model, $relatedModels = [], $cacheKeySuffix = '', $perPage = 5)
    {
        $currentPage = request()->get('page', 1);
        $cacheKey = $model . $cacheKeySuffix . '-page-' . $currentPage;

        $result = $this->cacheGet($cacheKey, function () use (
            $model,
            $relatedModels,
            $perPage
        ) {
            $query = $model::with($relatedModels);
            $this->applyFilters($query);

            return $query->paginate($perPage);
        }, 60);

        return $result;
    }

    private function applyFilters($query)
    {
        if (request()->has('search')) {
            $searchTerm = '%' . request()->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhereHas('currency', function ($q) use ($searchTerm) {
                        $q->where('code', 'like', $searchTerm);
                    });
            });
        }

        if (request()->has('sort')) {
            $query->orderBy(request()->order, request()->sort);
        }
    }

    /*

        public function optimizeQuery($model, $relatedModels = [], $cacheKeySuffix = '', $defaultPageSize = 5)
        {
            $cacheKey = $model . $cacheKeySuffix;

            if (request()->has('search')) {
                $cacheKey .= '-search-' . request()->search;
            }

            if (request()->has('sort')) {
                $cacheKey .= '-sort-' . request()->order . '-' . request()->sort;
            }

            if (request()->has('all') && request()->all == 'true') {
                $result = Cache::remember($cacheKey . '-all', 60, function () use ($model, $relatedModels) {
                    $query = $model::with($relatedModels);
                    $this->applyFilters($query);
                    return $query->get();
                });

                return $result;
            } else {
                $result = Cache::remember($cacheKey . '-paginate', 60, function () use ($model, $relatedModels, $defaultPageSize) {
                    $query = $model::with($relatedModels);
                    $this->applyFilters($query);
                    return $query->fastPaginate($defaultPageSize);
                });

                return $result;
            }
        }

        private function applyFilters($query, $relatedModels = [], $searchableFields = [])
        {
            if (request()->has('search')) {
                $searchTerm = '%' . request()->search . '%';
                $query->where(function ($q) use ($searchTerm, $searchableFields) {
                    foreach ($searchableFields as $field) {
                        $q->orWhere($field, 'like', $searchTerm);
                    }
                });
            }

            if (request()->has('sort')) {
                $query->orderBy(request()->order, request()->sort);
            }
        }

    */
}
