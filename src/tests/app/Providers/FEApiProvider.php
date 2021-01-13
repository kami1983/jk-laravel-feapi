<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FEApiProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
//        echo $this->app->basePath();
        $this->loadViewsFrom($this->app->basePath().'/app/JkFEApiLaravel/views', 'feapi');
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
