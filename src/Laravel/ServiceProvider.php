<?php
namespace Anthonypauwels\AirTable\Laravel;

use Exception;
use Anthonypauwels\AirTable\AirTable;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * ServiceProvider.
 *
 * @package Anthonypauwels\AirTable
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the DataLayer
     * @throws Exception
     */
    public function register()
    {
        $this->app->singleton('airtable', function () {
            return new AirTable( [
                'key' => config('airtable.key'),
                'base' => config('airtable.base'),
            ] );
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if ( $this->app->runningInConsole() ) {
            $this->publishes( [
                __DIR__ . '/config.php' => config_path('airtable.php'),
            ] );
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides():array
    {
        return [ AirTable::class ];
    }
}
