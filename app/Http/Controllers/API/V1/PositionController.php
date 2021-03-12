<?php

namespace App\Http\Controllers\API\V1;

use App\Constraints\CampaignStatusConstraint;
use App\Constraints\CommonConstraint;
use App\Http\Requests\Positions\PositionRequest;
use App\Models\Position;
use App\Models\Store;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PositionController extends BaseController
{
    /**
     * @var Position
     */
    protected $position = null;
    protected $fileService = null;

    /***
     * PositionController constructor.
     * @param Position $position
     * @param FileService $fileService
     */
    public function __construct(Position $position, FileService $fileService)
    {
        $this->middleware('auth:api');
        $this->position = $position;
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positions = $this->position->with('store')->latest()->paginate(10);

        return $this->sendResponse($positions,'Position list');
    }

    public function import(Request $request)
    {
        $filePath = $request->get('filePath');
        Artisan::call('import:position', compact('filePath'));

        return response()->json(['message' => 'success'], 200);

    }

    public function export(Request $request)
    {
        $query = Position::with(['bookings.campaign']);
        $positionTable = (new Position())->getTable();
        $storeTable = (new Store())->getTable();

        $from = intval($request->get('from_ts'));
        $to = intval($request->get('to_ts'));
        $status = $request->get('status');
        $filters = $request->get('filters');
        $sorts = $request->get('sort');

        if (!empty($status) || !empty($from) || !empty($to)) {
            if (!empty($status) && $status == CampaignStatusConstraint::STATUS_AVAILABLE) {
                $query->whereDoesntHave('bookings');
            }
            $query->orWhereHas('bookings', function ($q) use ($status, $from, $to) {
                $q->whereHas('campaign', function ($campaign_q) use ($status, $from, $to) {
                    if (!empty($status)) {
                        $campaign_q->where('status', $status == CampaignStatusConstraint::STATUS_AVAILABLE ? CampaignStatusConstraint::STATUS_CANCELLED : $status);
                    }

                    if (!empty($from)) {
                        $campaign_q->where('from_ts', '>=', $from);
                    }

                    if (!empty($to)) {
                        $campaign_q->where('to_ts', '<=', $to);
                    }
                });
            });
        }

        if (!empty($filters)) {
            if (!empty($filters['name'])) {
                $query->where("{$positionTable}.name", 'LIKE', "%{$filters['name']}%");
            }
            if (!empty($filters['channel'])) {
                $query->where("{$positionTable}.channel", 'LIKE', "%{$filters['channel']}%");
            }
            if (!empty($filters['store_code'])) {
                $query->where("{$storeTable}.code", 'LIKE', "%{$filters['store_code']}%");
            }
            if (!empty($filters['store_level'])) {
                $query->where("{$storeTable}.level", "{$filters['store_level']}");
            }
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort)
            {
                if (is_string($sort)) {
                    $sort = json_decode($sort, true);
                }

                if (array_key_exists('field', $sort) && array_key_exists('type', $sort)) {
                    if ($sort['field'] == 'store_level') {
                        $query->orderBy("{$storeTable}.level", $sort['type']);
                    } else {
                        $query->orderBy("{$positionTable}." . $sort['field'], $sort['type']);
                    }
                }
            }
        }

        $query->join($storeTable, "{$positionTable}.store_id", '=', "{$storeTable}.id");
        $query->select([
            "{$positionTable}.*",
            DB::raw("{$storeTable}.code as store_code"),
            DB::raw("{$storeTable}.name as store_name"),
            DB::raw("{$storeTable}.address as store_address"),
            DB::raw("{$storeTable}.ward as store_ward"),
            DB::raw("{$storeTable}.district as store_district"),
            DB::raw("{$storeTable}.province as store_province"),
            DB::raw("{$storeTable}.level as store_level"),
        ]);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        $rowNo = 1;

        $headers = $request->get('headers');
        $sheet->fromArray(array_column($headers, 'label'), null, 'A' . $rowNo++);
        $sheet->getRowDimension(1)->setRowHeight(40);
        $highestHeaders = $sheet->getHighestRowAndColumn();
        $sheet->getStyle('A1:' . $highestHeaders['column'] . $highestHeaders['row'])->applyFromArray([
            'font' => [
                'bold' => true,
                'name' => 'Tahoma',
                'size' => 12,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '993366',
                ],
            ],
        ]);

        for ($i = 'A'; $i <= $highestHeaders['column']; $i++)
        {
            $sheet->getColumnDimension($i)->setAutoSize(true);
        }

        $query->get()
            ->each(function ($row) use ($headers, &$sheet, &$rowNo) {
                $data = [];
                foreach ($headers as $header)
                {
                    if ($header['field'] == 'date_booking') {
                        $bookingsValidInTime = $row->bookings->count() ?
                            ($row->bookings->filter(function ($item) use ($header) {
                                return
                                    $item->from_ts <= $header['date_value'] &&
                                    $item->to_ts >= $header['date_value'];
                            })) : null;

                        if ($bookingsValidInTime && $bookingsValidInTime->count()) {
                            if (
                                $bookingsValidInTime->filter(function ($item) {
                                    return $item->campaign->status === CampaignStatusConstraint::STATUS_BOOKED;
                                })->count()
                            ) {
                                $data[] = 'Booked';
                            } elseif (
                                $bookingsValidInTime->filter(function ($item) {
                                    return $item->campaign->status === CampaignStatusConstraint::STATUS_RESERVED;
                                })->count()
                            ) {
                                $data[] = 'Reserved';
                            } else {
                                $data[] = 'Available';
                            }
                        } else {
                            $data[] = 'Available';
                        }
                    } else if ($header['field'] == 'store_full_address') {
                        $data[] = implode(', ', [
                            $row->store_address,
                            $row->store_ward,
                            $row->store_district,
                            $row->store_province,
                        ]);
                    } else {
                        $data[] = !empty($row->{$header['field']}) ? $row->{$header['field']} : null;
                    }
                }
                $sheet->fromArray($data, null, 'A' . $rowNo++);
        });

        // Store file
        $fileName = 'positions-' . time() . '.xlsx';
        $path = 'positions';
        if (!file_exists(storage_path('app/public') . DIRECTORY_SEPARATOR . $path)) {
            mkdir(storage_path('app/public') . DIRECTORY_SEPARATOR . $path);
        }
        $writer->save(storage_path('app/public') . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $fileName);

        $newFile = $this->fileService->createFile([
            'name' => $fileName,
            'disk_path' => $path,
            'type' => 'application/excel',
            'size' => 0,
            'extension' => 'xlsx',
            'file' => $fileName,
        ]);


        return $this->sendResponse([
            'file' => $newFile->id
        ], 'File');
    }

    public function list(Request $request)
    {
        // filter by
        // store_ids
        // Channels
        // from_ts, to_ts
        // store_ids=4,5,32,12&channels=Poster,Lightbox&from_ts=111&to_ts=2222

        $storeIds = $request->get('store_ids');
        $channels = $request->get('channels');
        $posIds = $request->get('position_ids');

        $condition = true;

        if ($posIds) {
            $array=array_map('intval', explode(',', $posIds));
            $array = implode(",", $array);
            $condition .= " AND positions.id in ($array)";
        }

        if ($storeIds) {
            $array=array_map('intval', explode(',', $storeIds));
            $array = implode(",", $array);
            $condition .= " AND store_id in ($array)";
        }

        if ($channels) {
            $array=explode(',', $channels);
            $array = implode("','", $array);
            $condition .= " AND channel in ('".$array."')";
        }

        $from = intval($request->get('from_ts'));
        $to = intval($request->get('to_ts'));

        if ($from && $to) {
            $fromToCondition = " AND positions.id not in (
                        select position_id from bookings
                        where
                            (from_ts<= %d and  %d <= (to_ts + buffer_ts)) or
                            (from_ts <= %d and %d <= (to_ts + buffer_ts))
                    )";
            $condition .= sprintf($fromToCondition, $from, $from, $to, $to);
        }

        $query = "
            SELECT
                positions.id AS id,
                positions.name AS name,
                positions.channel as channel,
                positions.price, positions.buffer_days,
                stores.name AS store_name
            FROM positions, stores
            WHERE positions.store_id = stores.id AND " . $condition . " ORDER BY id ";

	    Log::info("Query " . $query);

        $positions = DB::select($query);
        $data = [];
        foreach ($positions as $position) {
            $data[] = [
                'id' => $position->id,
                'name' => $position->name,
                'channel' => $position->channel,
                'price' => $position->price,
                'buffer_days' => $position->buffer_days,
                'store_name' => $position->store_name,
            ];
        }

        return $this->sendResponse($data, 'Position list');
    }

    public function list_v2(Request $request)
    {
        $query = Position::with(['bookings.campaign', 'store:id,code']);
        $positionTable = (new Position())->getTable();

        $from = intval($request->get('from_ts'));
        $to = intval($request->get('to_ts'));
        $status = $request->get('status');

        if (!empty($status) || !empty($from) || !empty($to)) {
            if (!empty($status) && $status == CampaignStatusConstraint::STATUS_AVAILABLE) {
                $query->whereDoesntHave('bookings');
            }
            $query->orWhereHas('bookings', function ($q) use ($status, $from, $to) {
               $q->whereHas('campaign', function ($campaign_q) use ($status, $from, $to) {
                   if (!empty($status)) {
                       $campaign_q->where('status', $status == CampaignStatusConstraint::STATUS_AVAILABLE ? CampaignStatusConstraint::STATUS_CANCELLED : $status);
                   }

                   if (!empty($from)) {
                       $campaign_q->where('from_ts', '>=', $from);
                   }

                   if (!empty($to)) {
                       $campaign_q->where('to_ts', '<=', $to);
                   }
               });
            });
        }

        $storeTable = (new Store())->getTable();
        $query->join($storeTable, "{$positionTable}.store_id", '=', "{$storeTable}.id");
        $query->select([
            "{$positionTable}.*",
            DB::raw("{$storeTable}.code as store_code"),
            DB::raw("{$storeTable}.name as store_name"),
            DB::raw("{$storeTable}.address as store_address"),
            DB::raw("{$storeTable}.ward as store_ward"),
            DB::raw("{$storeTable}.district as store_district"),
            DB::raw("{$storeTable}.province as store_province"),
            DB::raw("{$storeTable}.level as store_level"),
        ]);

        if (!empty($request->get('sort'))) {
            foreach ($request->get('sort') as $sort) {
                $sort = json_decode($sort, true);
                if (array_key_exists('field', $sort) && array_key_exists('type', $sort)) {
                    $query->orderBy($positionTable.".".$sort['field'], $sort['type']);
                }
            }
        }

        if (!empty($request->get('columnFilters'))) {
            $columnFilters = json_decode($request->get('columnFilters'), true);
            foreach ($columnFilters as $field => $value) {
                if (in_array($field, ['store_code', 'store_level'])) {
                    $query->where($storeTable.".".str_replace('store_', '', $field), 'LIKE', '%'.$value.'%');
                } else {
                    $query->where($positionTable.".".$field, 'LIKE', '%'.$value.'%');
                }
            }
        }

        $perPage = $request->get('perPage', CommonConstraint::PER_PAGE);

        return $this->sendResponse($query->paginate($perPage), 'Position list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @param PositionRequest $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function store(PositionRequest $request)
    {
        try{
            $position = $this->position->create([
                'name' => $request->get('name'),
                'description'=> $request->get('description'),
                'status' =>  $request->get('status') ?? Position::AVAILABLE,
                'image_url'=> $request->get('image_url'),
                'store_id' => $request->get('store')['id'],
                'channel'=> $request->get('channel'),
                'buffer_days' =>  $request->get('buffer_days'),
                'unit'=> $request->get('unit'),
                'price'=> $request->get('price'),
            ]);

            return $this->sendResponse($position, 'Position Created Successfully');
        } catch (Exception $e) {
            Log::error($e);
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PositionRequest $request, $id)
    {
        $position = $this->position->findOrFail($id);
        $data = [
            'name' => $request->get('name'),
            'description'=> $request->get('description'),
            'status' =>  $request->get('status') ?? Position::AVAILABLE,
            'image_url'=> $request->get('image_url'),
            'store_id' => $request->get('store')['id'],
            'channel'=> $request->get('channel'),
            'buffer_days' =>  $request->get('buffer_days'),
            'unit'=> $request->get('unit'),
            'price'=> $request->get('price'),
        ];

        $position->update($data);

        return $this->sendResponse($position, 'Position Information has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $this->authorize('isAdmin');
        $position = $this->position->findOrFail($id);
        $position->delete();

        return $this->sendResponse($position, 'Position has been Deleted');
    }

    public function getStatuses()
    {
        $statuses = CampaignStatusConstraint::getAll();
        $statuses[] = [
            'value' => CampaignStatusConstraint::STATUS_AVAILABLE,
            'text' => ucfirst(CampaignStatusConstraint::STATUS_AVAILABLE),
        ];
        return $this->sendResponse($statuses, 'Positions Statuses List');
    }
}
