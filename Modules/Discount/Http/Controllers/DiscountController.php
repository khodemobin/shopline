<?php

namespace Modules\Discount\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Modules\Discount\Http\Requests\DiscountRequest;
use Modules\Discount\Models\Discount;
use Modules\Discount\Repositories\DiscountRepoEloquentInterface;
use Modules\Discount\Services\DiscountService;
use Modules\Share\Http\Controllers\Controller;
use Modules\Share\Responses\AjaxResponses;
use Modules\Share\Traits\SuccessToastMessageWithRedirectTrait;

class DiscountController extends Controller
{
    use SuccessToastMessageWithRedirectTrait;

    private string $redirectRoute = 'discounts.index';

    /**
     * Get class.
     */
    private string $class = Discount::class;

    /**
     * Get discount repo eloquent.
     */
    protected DiscountRepoEloquentInterface $repo;

    /**
     * Get discount service.
     */
    protected DiscountService $service;

    public function __construct(DiscountRepoEloquentInterface $discountRepoEloquent, DiscountService $discountService)
    {
        $this->repo = $discountRepoEloquent;
        $this->service = $discountService;
    }

    /**
     * Get the latest discounts with show view page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('manage', $this->class);

        return view('Discount::index', ['discounts' => $this->repo->getLatest()->paginate()]);
    }

    /**
     * Show create view page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('manage', $this->class);

        return view('Discount::create');
    }

    /**
     * Store new discount by request & show success alert with redirect.
     *
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store(DiscountRequest $request)
    {
        $this->authorize('manage', $this->class);
        $this->service->store($request->validated());

        return $this->successMessageWithRedirect('Create discount');
    }

    /**
     * Show edit view page.
     *
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */
    public function edit(Discount $discount)
    {
        $this->authorize('manage', $this->class);

        return view('Discout::edit', compact('discount'));
    }

    /**
     * Update discount with success alert with redirect.
     *
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update(DiscountRequest $request, Discount $discount)
    {
        $this->authorize('manage', $this->class);
        $this->service->update($request->validated(), $discount->id);

        return $this->successMessageWithRedirect('Update discount');
    }

    /**
     * Delete discount with json response.
     *
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Discount $discount)
    {
        $this->authorize('manage', $this->class);
        $discount->delete();

        return AjaxResponses::SuccessResponse();
    }
}
