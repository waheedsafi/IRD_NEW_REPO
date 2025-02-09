<?php

namespace App\Providers;

use App\Repositories\ngo\NgoRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ngo\NgoRepositoryInterface;
use App\Repositories\task\PendingTaskRepository;
use App\Repositories\task\PendingTaskRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NgoRepositoryInterface::class, NgoRepository::class);
        $this->app->bind(PendingTaskRepositoryInterface::class, PendingTaskRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
