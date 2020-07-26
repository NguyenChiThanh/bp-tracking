<?php

namespace App\Console\Commands;

use App\Console\Commands\Services\SyncDistrictService;
use App\Console\Commands\Services\SyncProvinceService;
use App\Console\Commands\Services\SyncWardService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Log\Logger;

class SyncLocationCommand extends Command
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
    protected $signature = 'sync:location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync location from ERP';

    /**
     * SyncLocationCommand constructor.
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
        $syncLocationServices = $this->createServices();

        foreach ($syncLocationServices as $service) {
            $service->syncData();
        }

        return;
    }

    /**
     * @return array
     */
    private function createServices()
    {
        return [
            new SyncProvinceService($this->logger, $this->guzzleClient),
            new SyncDistrictService($this->logger, $this->guzzleClient),
            new SyncWardService($this->logger, $this->guzzleClient),
        ];
    }
}
