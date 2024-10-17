<?php

namespace Modules\Contact\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Modules\Contact\Models\Contact;
use Modules\Contact\Repositories\ContactRepoInterface;
use Modules\Contact\Services\ContactServiceInterface;
use Modules\Share\Http\Controllers\Controller;
use Modules\Share\Responses\AjaxResponses;

class ContactController extends Controller
{
    private string $class = Contact::class;

    protected ContactRepoInterface $repo;

    public function __construct(ContactRepoInterface $contactRepo)
    {
        $this->repo = $contactRepo;
    }

    /**
     * Get the latest contacts with show view page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('manage', $this->class);

        return view('Contact::index', ['contacts' => $this->repo->getLatest()->notRead()->paginate()]);
    }

    /**
     * Remove contact by route model binding.
     *
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     * @throws \Throwable
     */
    public function destroy(Contact $contact)
    {
        $this->authorize('manage', $this->class);
        $contact->deleteOrFail();

        return AjaxResponses::SuccessResponse();
    }

    /**
     * Update is_read with route model binding.
     *
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function updateIsRead(Contact $contact)
    {
        $this->authorize('manage', $this->class);
        resolve(ContactServiceInterface::class)->updateIsRead($contact);

        return AjaxResponses::SuccessResponse();
    }
}
