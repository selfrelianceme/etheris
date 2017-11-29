<?php

namespace Selfreliance\Etheris;
use Illuminate\Support\ServiceProvider;

class EtherisServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        include __DIR__ . '/routes.php';
        $this->app->make('Selfreliance\Etheris\Etheris');

        $this->publishes([
            __DIR__.'/config/etheris.php' => config_path('etheris.php'),
        ], 'config');
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