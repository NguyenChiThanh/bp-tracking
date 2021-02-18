<?php


namespace App\Console\Commands;


use App\Constraints\ImportTypesConstraint;
use App\Imports\Campaign\CampaignImport;
use App\Models\Utility;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utility:import {utility_id : utility need to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import from xlsx file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $utility_id = $this->argument('utility_id');
        $utility = Utility::find($utility_id);

        if (empty($utility)) {
            throw new \Exception('Utility ' . $utility_id . ' not found for importing');
        }

        // Set locale
        app()->setLocale($utility->language);
        switch ($utility->type) {
            case ImportTypesConstraint::IMPORT_CAMPAIGN:
                $result = (new CampaignImport)($utility);
                break;
        }

        if (!empty($result)) {
            $this->info('Import successfully');
        } else {
            $this->info('Import failed');
        }

        return true;
    }
}
