<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Yajra\Oci8\Connectors\OracleConnector;
use Yajra\Oci8\Oci8Connection;

class OracleServiceProvider extends ServiceProvider
{
    protected $defer = true;
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
        $this->app->bind('oracle', function(){
                $config = config('oracle.oracle');
                $connector = new OracleConnector();
                $connection = $connector->connect($config);
                $oracle = new Oci8Connection($connection, $config['database'], $config['prefix']);
                $pdo = $oracle->getPdo();
                // set query case insensitive
                $stmt = $pdo->prepare("alter session set NLS_SORT=BINARY_CI");
                $stmt->execute();
                $stmt = $pdo->prepare("alter session set NLS_COMP=LINGUISTIC");
                $stmt->execute();
                return $oracle;
        });
    }

    /**
     * Get service provided by the provider
     *
     * @return array
     */
    public function provides()
    {
        return [Oci8Connection::class]; // TODO: Change the autogenerated stub
    }

}