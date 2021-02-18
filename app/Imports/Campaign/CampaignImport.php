<?php


namespace App\Imports\Campaign;


use App\Imports\BaseImport;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Position;
use App\Models\Utility;
use App\Services\CampaignService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Throwable;
use DB;

class CampaignImport extends BaseImport
{
    const CAMPAIGN_LIST_SHEET_NAME = 'Sheet1';

    private $campaignService;
    private $campaignCollection;

    public $total_imported;
    public $header;

    public function __construct()
    {
        $this->options = [
            'chunkSize' => config('constant.SETTINGS.batch_size'),
            'required_sheets' => [
                self::CAMPAIGN_LIST_SHEET_NAME => [
                    'name' => 'CampaignList',
                    'header' => [
                        'name' => 'Campaign Name',
                        'contract_code' => 'Contract Code',
                        'company_name' => 'Company Name',
                        'license_code' => 'License Code',
                        'brand_code' => 'Brand Code',
                        'from_ts' => 'From Date Time',
                        'to_ts' => 'To Date Time',
                        'position_name' => 'Position Name',
                        'position_price' => 'Position Price',
                        'discount_type' => 'Discount Type',
                        'discount_value' => 'Discount Value',
                        'discount_max' => 'Discount Max',
                        'total_discount' => 'Total Discount',
                        'total_price' => 'Total Price',
                        'total_price_by_user' => 'Total Price by User',
                        'status' => 'Status',
                    ]
                ],
            ]
        ];
        $this->results = [
            self::CAMPAIGN_LIST_SHEET_NAME => [
                'total' => 0,
                'new' => 0,
                'update' => 0,
            ],
        ];
        $this->campaignService = new CampaignService();
        $this->campaignCollection = collect([]);
        $this->total_imported = 0;
    }

    public function __invoke(Utility $utility): bool
    {
        try {
            // STEP 1: Verification and initialization
            // Initial all necessary variables
            $this->init($utility);

            $this->getLogger()->info('******************** START IMPORT CAMPAIGNS ********************');
            $this->getLogger()->info('Utility: '.$utility->getKey());

            // Validate SHEETS
            $this->validateRequiredSheets();

            // Validate HEADERS
            $this->validateHeaderSheets();

            // We will collect data first then will process insert to database later
            $this->firstScan();

            // STEP 2: Process importing
            $this->processImport();

            $utility->finalizeImportProcess($this->getResults(), $this->total_imported);
            return true;
        } catch (Throwable $exception) {
            $utility->errorImport($exception);
            return false;
        }
    }

    private function firstScan()
    {
        // Count total of rows by iterating all rows
        // In addition, collect list of:
        // + parent code (code here is barcode, will be switched to asset code soon)
        // + location code
        // + category code
        // + department code
        // + model code
        // + uom code
        // + supplier code
        // + manufacturer code
        // + owner code
        // + original code
        // + status code
        // + user code
        // If file is empty, it cannot pass previous verification step
        $required_sheets = $this->options['required_sheets'];
        foreach ($required_sheets as $kr => $required_sheet) {
            $header = $required_sheet['header'];
            // must using importSheet_fastexcel_withheader() in order to access cell's value via index
            importSheet_fastexcel_withheader($required_sheet['sheet'],
                function ($line, $key) use ($header, &$sheet_total) {
                    if ($key > 2) {
                        $data = [];
                        foreach ($header as $field => $title) {
                            $data[$field] = $line[$title];
                        }
                        $this->getCampaignCollection()->push($data);
                    }
                });
        }
    }

    private function processImport()
    {
        $data = $this->getCampaignCollection()->groupBy('name');
        $total = $data->count();

        $this->getLogger()->info('Total of rows (exclude header) for all valid sheets: '.$total);
        $this->getUtility()->initializeImportInProgress($total);
        $this->results[self::CAMPAIGN_LIST_SHEET_NAME]['total'] = $total;

        $brands = Brand::all([
            'id',
            DB::raw('UPPER(name) as name'),
        ]);

        foreach ($data as $campaign_name => $rows) {
            try {
                $campaignArr = $rows->first();
                $campaignArr['user_id'] = $this->getUtility()->user_id;
                $campaignArr['position_list'] = $this->getPositionList($rows);
                $brand = $brands->firstWhere('name', ucwords($campaignArr['brand_code']));
                $campaignArr['brand'] = !empty($brand) ? $brand->toArray() : null;
                $campaignArr['status'] = [
                    'value' => strtolower($campaignArr['status'])
                ];
                if ($campaignArr['from_ts'] instanceof \DateTime) {
                    $campaignArr['from_ts'] = $campaignArr['from_ts']->getTimestamp();
                } else {
                    $campaignArr['from_ts'] = Carbon::createFromFormat('d/m/Y', $campaignArr['from_ts'])->getTimestamp();
                }
                if ($campaignArr['to_ts'] instanceof \DateTime) {
                    $campaignArr['to_ts'] = $campaignArr['to_ts']->getTimestamp();
                } else {
                    $campaignArr['to_ts'] = Carbon::createFromFormat('d/m/Y', $campaignArr['to_ts'])->getTimestamp();
                }

                $campaign = Campaign::query()->where('name', $campaign_name)->first();
                if (!empty($campaign)) {
                    $this->campaignService->storeCampaign($campaignArr, $campaign->getKey());
                    $this->results[self::CAMPAIGN_LIST_SHEET_NAME]['update'] += 1;
                } else {
                    $this->campaignService->storeCampaign($campaignArr);
                    $this->results[self::CAMPAIGN_LIST_SHEET_NAME]['new'] += 1;
                }

                $this->total_imported++;
            } catch (\Exception $exception) {
                $this->getLogger()->error('Error while importing: '.$exception->getMessage());
            }
        }
    }

    /**
     * @return Collection
     */
    public function getCampaignCollection(): Collection
    {
        return $this->campaignCollection;
    }

    public function getPositionList($rows): array
    {
        $position_list = Position::query()
            ->whereIn('name', $rows->pluck('position_name'))
            ->get();

        return $position_list->toArray();
    }
}
