<?php

namespace Modules\Comment\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Comment\Models\Comment;
use Modules\Comment\Policies\CommentPolicy;
use Modules\Comment\Repositories\CommentRepoEloquent;
use Modules\Comment\Repositories\CommentRepoEloquentInterface;

class CommentServiceProvider extends ServiceProvider
{
    /**
     * Get namespace for comment controllers.
     */
    private string $namespace = 'Modules\Comment\Http\Controllers';

    /**
     * Get migration path.
     */
    private string $migrationPath = '/../Database/Migrations';

    /**
     * Get view path.
     */
    private string $viewPath = '/../Resources/Views/';

    /**
     * Get route path.
     */
    private string $routePath = '/../Routes/comment_routes.php';

    /**
     * Get name.
     */
    private string $name = 'Comment';

    /**
     * Get route middleware/.
     *
     * @var array|string[]
     */
    private array $routeMiddleware = ['web', 'verify'];

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
        $this->loadPolicyFiles();

        $this->bindRepository();
    }

    /**
     * Boot service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setMenuForPanel();
        $this->setFactory();
    }

    /**
     * Set menu for panel.
     */
    private function setMenuForPanel(): void
    {
        config()->set('panelConfig.menus.comments', [
            'title' => 'Comment',
            'icon' => 'message-square',
            'url' => route('comments.index'),
        ]);
    }

    /**
     * Set factory.
     *
     * @return void
     */
    private function setFactory()
    {
        config()->set('shareConfig.factories.comment', [
            'model' => Comment::class,
            'count' => 1,
        ]);
    }

    /**
     * Load policy files.
     */
    private function loadPolicyFiles(): void
    {
        Gate::policy(Comment::class, CommentPolicy::class);
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
        $this->loadViewsFrom(__DIR__.$this->viewPath, $this->name);
    }

    /**
     * Load route files.
     */
    private function loadRouteFiles(): void
    {
        Route::middleware($this->routeMiddleware)
            ->namespace($this->namespace)
            ->group(__DIR__.$this->routePath);
    }

    /**
     * bind repository into interface.
     */
    private function bindRepository(): void
    {
        $this->app->bind(CommentRepoEloquentInterface::class, CommentRepoEloquent::class);
    }
}
