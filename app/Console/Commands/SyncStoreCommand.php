<?php

namespace App\Console\Commands;

use App\Console\Commands\Services\SyncStoreService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Log\Logger;

class SyncStoreCommand extends Command
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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:stores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync stores from ERP';

    /**
     * SyncStoreCommand constructor.
     * @param Logger $logger
     * @param Client $guzzleClient
     */
    public function __construct(Logger $logger, Client $guzzleClient)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $syncStoreService = new  SyncStoreService($this->logger, $this->guzzleClient);
        $syncStoreService->syncData();
        return;
    }
}
