<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Modules\Category\Repositories\CategoryRepoEloquentInterface;
use Modules\Product\Http\Requests\ProductRequest;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\ProductRepoEloquentInterface;
use Modules\Product\Services\ProductServiceInterface;
use Modules\Share\Http\Controllers\Controller;
use Modules\Share\Responses\AjaxResponses;
use Modules\Share\Services\ShareService;
use Modules\Share\Traits\SuccessToastMessageWithRedirectTrait;

class ProductController extends Controller
{
    use SuccessToastMessageWithRedirectTrait;

    private string $redirectRoute = 'products.index';

    /**
     * Get product class.
     */
    private string $class = Product::class;

    public ProductServiceInterface $service;

    public ProductRepoEloquentInterface $repo;

    public function __construct(ProductServiceInterface $productService, ProductRepoEloquentInterface $productRepo)
    {
        $this->service = $productService;
        $this->repo = $productRepo;
    }

    /**
     * Show index page of products.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('manage', $this->class);
        $products = $this->repo->getLatest()
            ->with(['categories', 'first_media', 'tags'])
            ->paginate();

        return view('Product::index', compact('products'));
    }

    /**
     * Show create product page.
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */
    public function create(CategoryRepoEloquentInterface $categoryRepo)
    {
        $this->authorize('manage', $this->class);
        $categories = $categoryRepo->getActiveCategories()->get();

        return view('Product::create', compact('categories'));
    }

    /**
     * Store product.
     *
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     * @throws AuthorizationException
     */
    public function store(ProductRequest $request)
    {
        $this->authorize('manage', $this->class);
        $this->uploadMedia($request);

        $product = $this->service->store($request->all());

        $this->service->attachCategoriesToProduct($request->categories, $product);
        $this->service->attachGalleriesToProduct($request->galleries, $product);

        $this->checkAndAttachAttributes($request->input('attributes'), $product);
        $this->checkAndAttachTags($request->tags, $product);

        return $this->successMessageWithRedirect('Create product');
    }

    /**
     * Find product by id with show edit product page.
     *
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */
    public function edit($id, CategoryRepoEloquentInterface $categoryRepo)
    {
        $this->authorize('manage', $this->class);

        $product = $this->repo->findById($id);
        $categories = $categoryRepo->getActiveCategories()->get();

        return view('Product::edit', compact(['product', 'categories']));
    }

    /**
     * Update product with request by id.
     *
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update(ProductRequest $request, $id)
    {
        $this->authorize('manage', $this->class);
        $product = $this->repo->findById($id);

        $this->checkAndUploadMediaForUpdate($request, $product, 'first_media', 'first_media_id');

        $this->service->update($request, $id);
        $this->service->firstOrCreateCategoriesToProduct($request->categories, $product);

        if (! is_null($request->galleries)) {
            $this->service->attachGalleriesToProduct($request->galleries, $product);
        }

        if (! empty($request->tags)) {
            $this->service->attachTagsToProduct($request->tags, $product);
        }

        return $this->successMessageWithRedirect('Update product');
    }

    /**
     * Delete product by id.
     *
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy($id)
    {
        $this->authorize('manage', $this->class);
        $this->repo->delete($id);

        return AjaxResponses::SuccessResponse();
    }

    // Private methods

    /**
     * Check & attach attributes.
     */
    private function checkAndAttachAttributes($attributes, $product): void
    {
        if (! is_null($attributes[0]['attributekeys'])) {
            $this->service->attachAttributesToProduct($attributes, $product);
        }
    }

    /**
     * Check & attach tags.
     */
    private function checkAndAttachTags($tags, $product): void
    {
        if (! empty($tags)) {
            $this->service->attachTagsToProduct($tags, $product);
        }
    }

    /**
     * Upload product medias.
     */
    private function uploadMedia(ProductRequest $request): void
    {
        ShareService::uploadMediaWithAddInRequest($request, 'first_media', 'first_media_id');
    }

    /**
     * Check & upload for media.
     */
    private function checkAndUploadMediaForUpdate(ProductRequest $request, $product, string $file, string $field): void
    {
        if (! is_null($request->first_media)) {
            ShareService::uploadMediaWithAddInRequest($request, $file, $field);
        } else {
            $request->request->add([$field => $product->first_media_id]);
        }
    }
}
