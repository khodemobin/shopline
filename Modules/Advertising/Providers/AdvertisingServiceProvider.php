<?php

namespace Modules\Advertising\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Advertising\Models\Advertising;
use Modules\Advertising\Policies\AdvertisingPolicy;
use Modules\Advertising\Repositories\AdvertisingRepoEloquent;
use Modules\Advertising\Repositories\AdvertisingRepoEloquentInterface;

class AdvertisingServiceProvider extends ServiceProvider
{
    public string $namespace = 'Modules\Advertising\Http\Controllers';

    public function register()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/Views/', 'Advertising');

        Route::middleware(['web', 'verify'])
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/advertising_routes.php');
        $this->bindRepository();
        $this->loadPolicyFiles();
    }

    private function bindRepository(): void
    {
        $this->app->bind(AdvertisingRepoEloquentInterface::class, AdvertisingRepoEloquent::class);
    }

    private function loadPolicyFiles(): void
    {
        Gate::policy(Advertising::class, AdvertisingPolicy::class);
    }
}
