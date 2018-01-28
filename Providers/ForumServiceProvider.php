<?php

namespace Modules\Forum\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Forum\Models\Post;
use Modules\Forum\Models\Thread;

class ForumServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerRouteBindings();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('netcore/module-forum.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'netcore.module-forum'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/forum');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/forum';
        }, config('view.paths')), [$sourcePath]), 'forum');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/forum');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'forum');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'forum');
        }
    }

    /**
     * Register route models and related things.
     *
     * @return void
     */
    public function registerRouteBindings(): void
    {
        $this->app['router']->bind('deletedThread', function ($id) {
            return Thread::withTrashed()->findOrFail($id);
        });

        $this->app['router']->bind('deletedPost', function ($id) {
            return Post::withTrashed()->findOrFail($id);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }
}
