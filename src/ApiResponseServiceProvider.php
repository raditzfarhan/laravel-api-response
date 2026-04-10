<?php

namespace RaditzFarhan\ApiResponse;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'laravel-api-response');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/laravel-api-response'),
            __DIR__ . '/../config/laravel-api-response.php' => config_path('laravel-api-response.php'),
        ]);

        Response::macro('api', function () {
            return app('ApiResponse');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-api-response.php', 'laravel-api-response');

        $this->app->bind('ApiResponse', function ($app) {
            $instance = new ApiResponse;
            $instance->configure(config('laravel-api-response', []));
            return $instance;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ApiResponse'];
    }
}
