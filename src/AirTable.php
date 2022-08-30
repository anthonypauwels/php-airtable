<?php
namespace Anthonypauwels\AirTable;

use InvalidArgumentException;

/**
 * AirTable manager
 *
 * @package Anthonypauwels\AirTable
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class AirTable
{
    const API_URL = 'https://api.airtable.com/v0/%s/';

    /** @var Base[] */
    protected array $bases = [];

    /** @var array */
    protected array $options = [];

    /**
     * AirTable manager constructor
     *
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function __construct(array $options)
    {
        $mandatory_keys = ['url', 'key', 'base'];
        $options_keys = array_keys( $options );

        if ( !array_are_identical( $mandatory_keys, $options_keys ) ) {
            $missing_keys = array_diff_key( $mandatory_keys, $options_keys );

            throw new InvalidArgumentException( sprintf( 'Missing options %s in AirTable class', implode(', ', $missing_keys ) ) );
        }

        $this->options = $options;
    }

    /**
     * Get a builder for a table from the default base
     *
     * @param string $table_name
     * @return Builder
     */
    public function table(string $table_name): Builder
    {
        return $this->on( $this->options['base'] )->table( $table_name );
    }

    /**
     * Get a base
     *
     * @param string $base_id
     * @return Base
     */
    public function on(string $base_id): Base
    {
        if ( isset( $this->bases[ $base_id ] ) ) {
            return $this->bases[ $base_id ];
        }

        return $this->bases[ $base_id ] = new Base(
            new Client(
                sprintf( $this->options['url'], $base_id ),
                $this->options['key']
            )
        );
    }
}
