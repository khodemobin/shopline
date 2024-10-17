<?php

namespace Modules\Panel\Http\Controllers;

use Modules\Panel\Models\Panel;
use Modules\Share\Http\Controllers\Controller;

class PanelController extends Controller
{
    /**
     * Show the panel page & get some data.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function __invoke()
    {
        $this->authorize('manage', Panel::class);

        return view('Panel::index');
    }
}
