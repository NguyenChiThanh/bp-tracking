<?php

namespace App\Console\Commands;

use App\Console\Commands\Services\SyncBrandService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Log\Logger;

class SyncBrandCommand extends Command
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
    protected $signature = 'sync:brand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync brand from ERP';

    /**
     * SyncBrandCommand constructor.
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
        $syncBrandService = new SyncBrandService(
            $this->logger,
            $this->guzzleClient
        );
        return $syncBrandService->syncData();
    }
}
