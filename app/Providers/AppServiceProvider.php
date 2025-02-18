<?php

namespace App\Providers;

use App\Repositories\ngo\NgoRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\User\UserRepository;
use App\Repositories\ngo\NgoRepositoryInterface;
use App\Repositories\Task\PendingTaskRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\Task\PendingTaskRepositoryInterface;
use App\Repositories\Permission\PermissionRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NgoRepositoryInterface::class, NgoRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
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
