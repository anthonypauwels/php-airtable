<?php
namespace Anthonypauwels\AirTable;

/**
 * AirTable Base
 *
 * @package Anthonypauwels\AirTable
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class Base
{
    /** @var Client */
    protected Client $client;

    /**
     * Base constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a builder for a table
     *
     * @param string $table_name
     * @return Builder
     */
    public function table(string $table_name): Builder
    {
        return new Builder( $this->client, $table_name );
    }
}
