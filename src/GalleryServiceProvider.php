<?php

namespace Infinety\Gallery;

use Illuminate\Support\ServiceProvider;

class GalleryServiceProvider extends ServiceProvider
{


    protected $commands = [
        'Infinety\Gallery\Commands\migrate',
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        if (! $this->app->routesAreCached()) {
            require __DIR__.'/routes.php';
        }

        $this->loadViewsFrom(__DIR__.'/resources/views/', 'gallery');

        /**
         * Publishes Migrations files
         */
        $this->publishes([
            realpath(__DIR__.'/migration') => $this->app->databasePath().'/migrations',
        ]);

        /**
         * Publishes Lang files
         */
        $this->publishes([
            realpath(__DIR__.'/resources/lang') => $this->app->basePath().'/resources/lang'
        ]);

        /**
         * Piblishes Public Assets
         */
        $this->publishes([
            __DIR__.'/public' => public_path('gallery_assets'),
        ], 'public');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Infinety\Gallery\Controllers\GalleryController');
        $this->app->make('Infinety\Gallery\Controllers\CategoryController');
    }
}
