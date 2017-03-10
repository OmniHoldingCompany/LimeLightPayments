<?php

namespace OmniHolding\LimeLightPayments\Providers;

use Illuminate\Support\ServiceProvider;

class LimeLightPayments extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // migrations
        $this->loadMigrationsFrom(__DIR__.'/../../migrations');

        // views
        $this->loadViewsFrom(__DIR__.'/../views', 'LimeLightPayments');
        $this->publishes([
            __DIR__.'/../views/' => resource_path('views/payments'),
        ]);

        // config
        $this->publishes([
            __DIR__.'/../../config/limelight-payments.php' => config_path('limelight-payments.php'),
        ]);
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
