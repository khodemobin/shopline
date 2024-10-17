<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Hash;
use Modules\Share\Http\Controllers\Controller;
use Modules\Share\Responses\AjaxResponses;
use Modules\Share\Services\ShareService;
use Modules\User\Http\Requests\UserRequest;
use Modules\User\Models\User;
use Modules\User\Repositories\UserRepoEloquentInterface;
use Modules\User\Services\UserService;

class UserController extends Controller
{
    private string $class = User::class;

    public UserRepoEloquentInterface $repo;

    public UserService $service;

    public function __construct(UserRepoEloquentInterface $userRepo, UserService $userService)
    {
        $this->repo = $userRepo;
        $this->service = $userService;
    }

    /**
     * Get the latest users without user logged.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('manage', $this->class);
        $users = $this->repo->getLatestWithoutId(auth()->id())->paginate(25);

        return view('User::index', compact('users'));
    }

    /**
     * Show create view for user.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('manage', $this->class);

        return view('User::create');
    }

    /**
     * Store user by request.
     *
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store(UserRequest $request)
    {
        $this->authorize('manage', $this->class);
        $user = $this->service->store($request);

        if ($request->has('email_verified_at')) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return $this->successMessageWithRedirect('User created successfully');
    }

    /**
     * Show edit view & check user is exists by id.
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * @throws AuthorizationException
     */
    public function edit($id)
    {
        $this->authorize('manage', $this->class);
        $user = $this->repo->findById($id);

        return view('User::edit', compact('user'));
    }

    /**
     * Update user by request with id.
     *
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update(UserRequest $request, $id)
    {
        $this->authorize('manage', $this->class);
        $userId = $this->service->update($request, $id);

        if ($request->password) {
            $user = $this->repo->findById($userId);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return $this->successMessageWithRedirect('User updated successfully');
    }

    /**
     * Delete user by id.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy($id)
    {
        $this->authorize('manage', $this->class);
        $this->repo->delete($id);

        return AjaxResponses::SuccessResponse();
    }

    /**
     * Show success message with redirect.
     *
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function successMessageWithRedirect(string $title)
    {
        ShareService::successToast($title);

        return to_route('users.index');
    }
}
