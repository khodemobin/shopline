<?php

namespace Modules\Home\Repositories;

use Modules\Home\Models\Home;
use Modules\Share\Contracts\Interface\RepositoriesInterface;
use Modules\Share\Repositories\ShareRepo;

class HomeRepo implements RepositoriesInterface
{
    private string $class = Home::class;

    public function index()
    {

    }

    public function findById($id)
    {

    }

    public function delete($id)
    {

    }

    private function query()
    {
        return ShareRepo::query($this->class);
    }
}
