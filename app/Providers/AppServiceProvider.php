<?php

namespace App\Providers;

use App\Interfaces\ClientInterface;
use App\Interfaces\ResponseInterface;
use App\Services\Client;
use App\Services\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(ClientInterface::class, Client::class);
        $this->app->bind(ResponseInterface::class, Response::class);
    }
}
