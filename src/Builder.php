<?php
namespace Anthonypauwels\AirTable;

use RuntimeException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * AirTable query builder
 *
 * @package Anthonypauwels\AirTable
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class Builder
{
    /** @var Client */
    protected Client $client;

    /** @var string */
    protected string $table;

    /** @var bool */
    protected bool $typecast = false;

    /** @var integer */
    protected int $delay = 200000;

    /** @var array */
    protected array $fields = [];

    /** @var string */
    protected string $criteria;

    /** @var string */
    protected string $view;

    /** @var integer */
    protected int $limit;

    /** @var integer */
    protected int $offset;

    /** @var array */
    protected array $sort = [];

    /** @var array */
    protected array $operators = [
        '>', '<', '>=', '<=', '=', '!='
    ];

    /**
     * Builder constructor
     *
     * @param Client $client
     * @param string $table_name
     */
    public function __construct(Client $client, string $table_name)
    {
        $this->client = $client;
        $this->table = $table_name;
    }

    /**
     * Count the number of elements inside the query
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function count(): int
    {
        return count( $this->get() );
    }

    /**
     * If AirTable must perform an automatic data conversion from string values
     *
     * @param bool $value
     * @return $this
     */
    public function typecast(bool $value): Builder
    {
        $this->typecast = $value;

        return $this;
    }

    /**
     * Delay between request
     *
     * @param int $value
     * @return $this
     */
    public function delay(int $value): Builder
    {
        $this->delay = $value;

        return $this;
    }

    /**
     * Search for specific fields from records
     *
     * @param array|string $fields
     * @return $this
     */
    public function fields(array|string $fields): Builder
    {
        if ( !is_array( $fields ) ) {
            $fields = [ $fields ];
        }

        $this->fields = array_merge( $this->fields, $fields );

        return $this;
    }

    /**
     * Filter records using a logical where operation
     *
     * @param string $field
     * @param mixed $operator
     * @param null $value
     * @return $this
     */
    public function where(string $field, mixed $operator, $value = null): Builder
    {
        if ( !$this->invalidOperatorAndValue( $operator, $value ) ) {
            $value = $operator;
            $operator = '=';
        }

        $this->criteria = '{' . $field . '}' . $operator . '"' . $value . '"';

        return $this;
    }

    /**
     * Filter records using a raw query
     *
     * @param string $formula
     * @return $this
     */
    public function whereRaw(string $formula): Builder
    {
        $this->criteria = $formula;

        return $this;
    }

    /**
     * Determine if $operator is the where value or not
     *
     * @param string $operator
     * @param mixed $value
     * @return bool
     */
    protected function invalidOperatorAndValue(string $operator, mixed $value): bool
    {
        return is_null( $value ) && in_array( $operator, $this->operators ) && !in_array( $operator, [ '=', '!=' ] );
    }

    /**
     * Get records from a specific view
     *
     * @param string $view_name
     * @return $this
     */
    public function view(string $view_name): Builder
    {
        $this->view = $view_name;

        return $this;
    }

    /**
     * Order records by a field and direction
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $field, string $direction = 'asc'): Builder
    {
        $this->sort[] = compact('field', 'direction');

        return $this;
    }

    /**
     * Set the limit value to get a limited number of records
     *
     * @param int $value
     * @return $this
     */
    public function limit(int $value): Builder
    {
        $this->limit = $value;

        return $this;
    }

    /**
     * Alias to limit method
     *
     * @param int $value
     * @return $this
     */
    public function take(int $value): Builder
    {
        return $this->limit( $value );
    }

    /**
     * Set the offset value to get records from a specific page
     *
     * @param int $value
     * @return $this
     */
    public function offset(int $value): Builder
    {
        $this->offset = $value;

        return $this;
    }

    /**
     * Alias to offset method
     *
     * @param int $value
     * @return $this
     */
    public function skip(int $value): Builder
    {
        return $this->offset( $value );
    }

    /**
     * Get records with a limit of 100 by page
     *
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function get(): array
    {
        $records = [];

        do  {
            $params = [];

            if ( !empty( $this->criteria ) ) {
                $params['filterByFormula'] = $this->criteria;
            }

            if ( !empty( $this->view ) ) {
                $params['view'] = $this->view;
            }

            if ( !empty( $this->sort ) ) {
                $params['sort'] = $this->sort;
            }

            if ( !empty( $this->limit ) ) {
                $params['pageSize'] = $this->limit;
            }

            if ( !empty( $this->offset ) ) {
                $params['offset'] = $this->offset;
            }

            $response = $this->client->sendGet( $this->table, $params );

            if ( isset( $response['records'] ) ) {
                $records += $response['records'];
            }

            if ( isset( $response['offset'] ) ) {
                $this->offset = $response['offset'];

                usleep( $this->delay );
            } else {
                $this->offset = false;
            }

        } while( $this->offset );

        foreach ( $records as $index => $record ) {
            $records[ $index ] = $this->formatRecord( $record );
        }

        return $records;
    }

    /**
     * Method alias to get, return all records
     *
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function all(): array
    {
        return $this->get();
    }

    /**
     * Get the first record
     *
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function first(): array
    {
        $records = $this->get();

        return $this->formatRecord( array_shift( $records ) );
    }

    /**
     * Find a record using his ID
     *
     * @param string $id
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function find(string $id): array
    {
        $record = $this->client->sendGet( $this->table . '/' . $id );

        return $this->formatRecord( $record );
    }

    /**
     * Insert a record
     *
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function insert(array $data): array
    {
        $record = $this->client->sendPost( $this->table, [
            'json' => [
                'fields' => (object) $data,
                'typecast' => $this->typecast,
            ]
        ] );

        return $this->formatRecord( $record );
    }

    /**
     * Update a record or many records. Destructive way
     *
     * @param array|string $id
     * @param array|null $data
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function update(array|string $id, array $data = null): array
    {
        if ( is_array( $id ) && $data === null ) {
            return $this->performMassUpdate( $id, 'sendPut' );
        }

        $record = $this->client->sendPut( $this->table . '/' . $id, [
            'json' => [
                'fields' => (object) $data,
                'typecast' => $this->typecast,
            ]
        ] );

        return $this->formatRecord( $record );
    }

    /**
     * Patch a single record or many records
     *
     * @param array|string $id
     * @param array|null $data
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function patch(array|string $id, array $data = null): array
    {
        if ( is_array( $id ) && $data === null ) {
            return $this->performMassUpdate( $id, 'sendPath' );
        }

        $record = $this->client->sendPatch( $this->table . '/' . $id, [
            'json' => [
                'fields' => (object) $data,
                'typecast' => $this->typecast,
            ]
        ] );

        return $this->formatRecord( $record );
    }

    /**
     * Perform a mass update using sendPut or sendPath method
     *
     * @param array $data
     * @param string $method
     * @return array
     */
    protected function performMassUpdate(array $data, string $method = 'sendPut'): array
    {
        $records = [];
        $chunks = array_chunk( $data, 10 );

        foreach ( $chunks as $key => $item ) {
            $params =  [
                'json' => [
                    'fields' => (object) $item,
                    'typecast' => $this->typecast,
                ]
            ];

            $response = $this->client->$method( $this->table, $params );
            $records += $response['records'];

            if ( isset( $chunks[ $key + 1 ] ) ) {
                usleep( $this->delay );
            }
        }

        foreach ( $records as $index => $record ) {
            $records[ $index ] = $this->formatRecord( $record );
        }

        return $records;
    }

    /**
     * Delete a single record
     *
     * @param string $id
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function delete(string $id): array
    {
        return $this->client->sendDelete( $this->table . '/' . $id );
    }

    /**
     * Format the Record object to get "id" and "createdTime" in the same level as "fields"
     *
     * @param array $record
     * @return array
     */
    protected function formatRecord(array $record): array
    {
        if ( isset( $record['fields'] ) ) {
            return array_merge( $record['fields'], ['createdTime' => $record['createdTime'] ], ['id' => $record['id'] ] );
        }

        return $record;
    }
}
