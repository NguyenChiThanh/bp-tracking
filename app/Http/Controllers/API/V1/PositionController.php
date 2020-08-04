<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Positions\PositionRequest;
use App\Models\Position;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PositionController extends BaseController
{
    /**
     * @var Position
     */
    protected $position = null;

    /***
     * PositionController constructor.
     * @param Position $position
     */
    public function __construct(Position $position)
    {
        $this->middleware('auth:api');
        $this->position = $position;
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

    public function list(Request $request)
    {
        // filter by
        // store_ids
        // Channels
        // from_ts, to_ts
        // store_ids=4,5,32,12&channels=Poster,Lightbox&from_ts=111&to_ts=2222

        $storeIds = $request->get('store_ids');
        $channels = $request->get('channels');
        $fromTs = $request->get('from_ts');
        $toTs = $request->get('to_ts');
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
            WHERE positions.store_id = stores.id AND 1" . $condition . " ORDER BY id ";

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
                'status' =>  $request->get('status'),
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
            'status' =>  $request->get('status'),
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
}
