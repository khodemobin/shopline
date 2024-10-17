<?php

namespace Modules\Payment\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Payment\Gateways\Gateway;
use Modules\Payment\Repositories\PaymentRepoEloquent;
use Modules\Payment\Repositories\PaymentRepoEloquentInterface;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Namespace of user controllers.
     */
    private string $namespace = 'Modules\Payment\Http\Controllers';

    /**
     * Get route path.
     */
    private string $routePath = '/../Routes/payment_routes.php';

    /**
     * Get view path.
     */
    private string $viewPath = '/../Resources/Views/';

    /**
     * Get migration path.
     */
    private string $migrationPath = '/../Database/Migrations';

    /**
     * Namespace of payment view files.
     */
    private string $namespaceUserView = 'Payment';

    /**
     * Array of middleware routes.
     *
     * @var array|string[]
     */
    private array $middlewareRoute = ['web', 'verify'];

    /**
     * Register files.
     *
     * @return void
     */
    public function register()
    {
        $this->loadMigrationFiles();
        $this->loadViewFiles();
        $this->loadRouteFiles();

        $this->bindRepositories();
        $this->setGateway();
    }

    /**
     * Load migration files.
     */
    private function loadMigrationFiles(): void
    {
        $this->loadMigrationsFrom(__DIR__.$this->migrationPath);
    }

    /**
     * Load view files.
     */
    private function loadViewFiles(): void
    {
        $this->loadViewsFrom(__DIR__.$this->viewPath, $this->namespaceUserView);
    }

    /**
     * Load route files.
     */
    private function loadRouteFiles(): void
    {
        Route::middleware($this->middlewareRoute)
            ->namespace($this->namespace)
            ->group(__DIR__.$this->routePath);
    }

    /**
     * Bind repositories.
     *
     * @return void
     */
    private function bindRepositories()
    {
        app()->bind(PaymentRepoEloquentInterface::class, PaymentRepoEloquent::class);
    }

    /**
     * Set gatewaty.
     *
     * @return void
     */
    private function setGateway()
    {
        $this->app->singleton(Gateway::class, static function ($app) {
            //            return new PayClass();
        });
    }
}
