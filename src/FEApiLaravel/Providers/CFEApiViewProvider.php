<?php

namespace KLib\FEApiLaravel\Providers;

use Illuminate\Support\ServiceProvider;

class CFEApiViewProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
//        echo $this->app->basePath();
        $this->loadViewsFrom(__DIR__.'/../views', 'feapi');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
