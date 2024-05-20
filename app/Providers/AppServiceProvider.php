<?php

namespace App\Providers;

use App\Services\V1\ProposeCombinationHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(ProposeCombinationHandler::class)
            ->needs('$gameTime')
            ->giveConfig('app.game_time');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
