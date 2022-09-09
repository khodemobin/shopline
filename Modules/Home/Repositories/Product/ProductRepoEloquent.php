<?php

namespace Modules\Home\Repositories\Product;

use Modules\Product\Models\Product;

class ProductRepoEloquent implements ProductRepoEloquentInterface
{
    /**
     * Get active latest products.
     *
     * @return mixed
     */
    public function getLatest()
    {
        return Product::query()
            ->active()
            ->latest();
    }

    /**
     * Find product by sku & slug.
     *
     * @param  $sku
     * @param  $slug
     * @return mixed
     */
    public function findProductBySkuWithSlug($sku, $slug)
    {
        return Product::query()
            ->with(['galleries', 'categories', 'tags', 'attributes'])
            ->withCount('rates')
            ->active()
            ->where('sku', (int) $sku)
            ->where('slug', $slug)
            ->first();
    }
}
