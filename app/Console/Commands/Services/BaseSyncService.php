<?php

namespace App\Console\Commands\Services;

use GuzzleHttp\Client;
use Illuminate\Log\Logger;

class BaseSyncService
{
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var Client
     */
    protected $guzzleClient;
    /**
     * @var string
     */
    protected $graphqlEndpoint;
    /**
     * @var string[]
     */
    protected $options;

    /**
     * BaseSyncService constructor.
     * @param Logger $logger
     * @param Client $guzzleClient
     */
    public function __construct(Logger $logger, Client $guzzleClient)
    {
        $this->logger = $logger;
        $this->guzzleClient = $guzzleClient;
        $this->graphqlEndpoint = env('GRAPHQL_ENDPOINT');
        $this->options = [
            'headers' => [
                'content-type' => 'application/json'
            ],
        ];
    }
}
