<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AccessServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Repositories\Storage\ERPRepositoryContract::class,
            \App\Repositories\Storage\ERPRepository::class
        );
        $this->app->bind(
            \App\Repositories\Storage\MStorageRepositoryContract::class,
            \App\Repositories\Storage\MStorageRepository::class
        );
        $this->app->bind(
            \App\Repositories\Smartchannel\SmartchannelRepositoryContract::class,
            \App\Repositories\Smartchannel\SmartchannelRepository::class
        );
        $this->app->bind(
            \App\Repositories\Storage\StorageRepositoryContract::class,
            \App\Repositories\Storage\StorageRepository::class
        );
        $this->app->bind(
            \App\Repositories\Product\ProductRepositoryContract::class,
            \App\Repositories\Product\ProductRepository::class
        );
        $this->app->bind(
            \App\Repositories\User\UserRepositoryContract::class,
            \App\Repositories\User\UserRepository::class
        );
        $this->app->bind(
            \App\Repositories\Role\RoleRepositoryContract::class,
            \App\Repositories\Role\RoleRepository::class
        );
        $this->app->bind(
            \App\Repositories\Department\DepartmentRepositoryContract::class,
            \App\Repositories\Department\DepartmentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Setting\SettingRepositoryContract::class,
            \App\Repositories\Setting\SettingRepository::class
        );
        $this->app->bind(
            \App\Repositories\Task\TaskRepositoryContract::class,
            \App\Repositories\Task\TaskRepository::class
        );
        $this->app->bind(
            \App\Repositories\Client\ClientRepositoryContract::class,
            \App\Repositories\Client\ClientRepository::class
        );
        $this->app->bind(
            \App\Repositories\Lead\LeadRepositoryContract::class,
            \App\Repositories\Lead\LeadRepository::class
        );
        $this->app->bind(
            \App\Repositories\Invoice\InvoiceRepositoryContract::class,
            \App\Repositories\Invoice\InvoiceRepository::class
        );
    }
}
