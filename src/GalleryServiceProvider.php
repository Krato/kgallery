<?php

namespace Infinety\Gallery;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class GalleryServiceProvider extends ServiceProvider
{
    protected $commands = [
        'Infinety\Gallery\Commands\migrate',
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'gallery'
        );

        $this->loadViewsFrom(__DIR__.'/resources/views/', 'gallery');

        /*
         * Publishes Migrations files
         */
        $this->publishes([
            realpath(__DIR__.'/migration') => $this->app->databasePath().'/migrations',
        ]);

        /*
         * Publishes Lang files
         */
        $this->publishes([
            realpath(__DIR__.'/resources/lang') => $this->app->basePath().'/resources/lang',
        ]);

        /*
         * Piblishes Public Assets
         */
        $this->publishes([
            __DIR__.'/public' => public_path('gallery_assets'),
        ], 'public');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->setupRoutes($this->app->router);
        $this->app->bind('Gallery', function ($app) {
            return new Gallery($app);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     */
    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Infinety\Gallery\Controllers'], function ($router) {
            if (!$this->app->routesAreCached()) {
                require __DIR__.'/routes.php';
            }
        });
    }
}
