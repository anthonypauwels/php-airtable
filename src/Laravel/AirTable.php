<?php
namespace Anthonypauwels\AirTable\Laravel;

use Illuminate\Support\Facades\Facade;
use Anthonypauwels\AirTable\AirTable as AirTableManager;

/**
 * Facade.
 * Provide quick access methods to the AirTable class
 *
 * @method static AirTableManager table(string $table_name): Builder
 * @method static AirTableManager on(string $base_id): Base
 *
 * @package Anthonypauwels\AirTable
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class AirTable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor():string
    {
        return 'airtable';
    }
}
