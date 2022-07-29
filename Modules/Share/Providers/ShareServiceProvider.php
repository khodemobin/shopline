<?php

namespace Modules\Share\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Modules\Share\Console\Commands\MakeModule;

class ShareServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/Views/', 'Share');
        $this->loadCommands();
        $this->loadShareComponents();
        $this->loadAuthComponents();
        $this->loadFactories();
    }

    /**
     * Load component about share.
     *
     * @return void
     */
    private function loadShareComponents()
    {
        $this->loadViewComponentsAs('share', [
            \Modules\Share\Components\Share\Error::class,
        ]);
    }

    /**
     * Load component about auth.
     *
     * @return void
     */
    private function loadAuthComponents()
    {
        $this->loadViewComponentsAs('auth', [
            \Modules\Share\Components\Auth\Input::class,
            \Modules\Share\Components\Auth\Button::class,
        ]);
    }

    /**
     * Load commands.
     *
     * @return void
     */
    private function loadCommands()
    {
        $this->commands([
            MakeModule::class
        ]);
    }

    /**
     * Load factories.
     *
     * @return void
     */
    private function loadFactories()
    {
        Factory::guessFactoryNamesUsing(static function (string $modelName) {
            return 'Modules\Share\Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });
    }
}
