<?php

namespace App\Console\Commands;

use App\Console\Commands\Services\DistrictService;
use App\Console\Commands\Services\ProvinceService;
use App\Console\Commands\Services\WardService;
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
        $services = $this->createServices();

        foreach ($services as $service) {
            $service->syncData();
        }
    }

    /**
     * @return array
     */
    private function createServices()
    {
        return [
            new ProvinceService($this->logger, $this->guzzleClient),
            new DistrictService($this->logger, $this->guzzleClient),
            new WardService($this->logger, $this->guzzleClient),
        ];
    }
}
