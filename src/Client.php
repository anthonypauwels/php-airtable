<?php
namespace Anthonypauwels\AirTable;

use RuntimeException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * AirTable API Client
 *
 * @package Anthonypauwels\AirTable
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class Client
{
    /** @var string */
    protected string $baseUri;

    /** @var string */
    protected string $apiKey;

    /** @var GuzzleClient */
    protected GuzzleClient $httpClient;

    public function __construct(string $base_uri, string $api_key)
    {
        $this->baseUri = $base_uri;
        $this->apiKey = $api_key;
    }

    /**
     * Send a GET request
     *
     * @param string $query_string
     * @param array $query_parameters
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function sendGet(string $query_string, array $query_parameters = [], array $data = []): array
    {
        if ( !empty( $query_parameters ) ) {
            $query_string .= '?';

            foreach ( $query_parameters as $key => $value ) {
                $query_string .= '&' . $key . '=' . urlencode( $value );
            }
        }

        $response = $this->getClient()->request( 'GET', $query_string, $data );

        return $this->handleResponse( $response );
    }

    /**
     * Send a POST request
     *
     * @param string $query_string
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function sendPost(string $query_string, array $data = []): array
    {
        $response = $this->getClient()->request( 'POST', $query_string, $data );

        return $this->handleResponse( $response );
    }

    /**
     * Send a PATCH request
     *
     * @param string $query_string
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function sendPatch(string $query_string, array $data = []): array
    {
        $response = $this->getClient()->request( 'PATCH', $query_string, $data );

        return $this->handleResponse( $response );
    }

    /**
     * Send a PUT request
     *
     * @param string $query_string
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function sendPut(string $query_string, array $data = []): array
    {
        $response = $this->getClient()->request( 'PUT', $query_string, $data );

        return $this->handleResponse( $response );
    }

    /**
     * Send a DELETE request
     *
     * @param string $query_string
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function sendDelete(string $query_string, array $data = []): array
    {
        $response = $this->getClient()->request( 'DELETE', $query_string, $data );

        return $this->handleResponse( $response );
    }

    /**
     * Setup Guzzle HTTP Client with base URI and API KEY and return it
     *
     * @return GuzzleClient
     */
    protected function getClient(): GuzzleClient
    {
        if ( !$this->httpClient ) {
            $this->httpClient = new GuzzleClient( [
                'base_uri' => $this->baseUri,
                'headers'  => [
                    'Authorization' => sprintf('Bearer %s', $this->apiKey ),
                    'Content-Type' => 'application/json',
                ]
            ] );
        }

        return $this->httpClient;
    }

    /**
     * Handle the response to convert JSON into readable array
     *
     * @param $response
     * @return array
     *
     * @throws RuntimeException
     */
    protected function handleResponse($response):array
    {
        $body = $response->getBody()->getContents();
        $content = json_decode( $body, true );

        if ( $response->getStatusCode() === 200 ) {
            return $content;
        }

        throw new RuntimeException( $content['message'] );
    }
}
