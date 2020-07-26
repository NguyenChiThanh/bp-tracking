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

    public function __construct($logger, $guzzleClient)
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
