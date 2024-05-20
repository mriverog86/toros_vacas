<?php

namespace App\Providers;

use App\Interfaces\V1\GameRepositoryInterface;
use App\Repository\V1\GameRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 *
 * Provider para resolver la implementación a usar para la interface GameRepositoryInterface
 *
 * @package App\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registra el servicio
     */
    public function register(): void
    {
        $this->app->bind(GameRepositoryInterface::class,GameRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
