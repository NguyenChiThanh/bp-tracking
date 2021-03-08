<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\Services\SyncPositionService;
use Illuminate\Log\Logger;
use GuzzleHttp\Client;


class SyncPositionCommand extends Command
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
    protected $signature = 'sync:positions {all?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Marketing Store position from OMS';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $syncPositionService = new SyncPositionService($this->logger, $this->guzzleClient);
        
        $arguments=$this->arguments();
        
        if(isset($arguments['all'])){

            $syncPositionService->syncData();

        }else{

            $syncDate=date('Y-m-d 00:00:00', strtotime('-1 days'));
            $syncPositionService->syncData($syncDate);

        }
      
    }
}
