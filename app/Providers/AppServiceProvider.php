<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Em produção (Hostinger com SSL) força HTTPS nas URLs geradas.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
