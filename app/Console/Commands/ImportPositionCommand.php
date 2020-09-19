<?php

namespace App\Console\Commands;

use App\Models\Position;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use \PhpOffice\PhpSpreadsheet\IOFactory;

class ImportPositionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:position {filePath : path to file need to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import positions from xlsx file';

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
        $filePath = $this->argument('filePath');
        $spreadsheet = IOFactory::load(base_path().'/storage/app/'.$filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        // Get the highest row and column numbers referenced in the worksheet
        // $highestRow = $worksheet->getHighestRow(); // e.g. 10
        // $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'

        for ($row = 2;; $row++) {
            $storeCode = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            if (empty($storeCode)) {
                break;
            }
            $channel = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            $positionName= $worksheet->getCellByColumnAndRow(3, $row)->getValue();
            $description = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
            $bufferDays = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
            $price = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
            $width = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
            $height = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
            $image = $worksheet->getCellByColumnAndRow(9, $row)->getValue();

            try {
                $store = Store::where('code', $storeCode)->firstOrFail();
                $position = Position::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'channel' => $channel,
                        'name' => $positionName,
                    ],
                    [
                        'name' => $positionName,
                        'description' => empty($description) ? '' : $description,
                        'status' => Position::AVAILABLE,
                        'image_url' => $image,
                        'store_id' => $store->id,
                        'channel' => $channel,
                        'buffer_days' => empty($bufferDays) ? 2 : $bufferDays,
                        'unit' => 'day',
                        'width' => $width,
                        'height' => $height,
                        'price' => empty($price) ? 0.0 : $price,
                    ]);
                Log::info("Position " . $position->name . " imported");
            } catch (ModelNotFoundException $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
