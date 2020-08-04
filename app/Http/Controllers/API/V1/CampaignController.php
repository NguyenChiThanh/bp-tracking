<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Campaigns\CampaignRequest;
use App\Models\Campaign;
use App\Models\Position;
use App\Models\Booking;


use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CampaignController extends BaseController
{
    protected $campaign = null;


    protected $booking = null;

    protected $position = null;

    /***
     * CampaiignController constructor.
     * @param Campaign $campaign
     */
    public function __construct(Campaign $campaign, Booking $booking, Position $position)
    {
        $this->middleware('auth:api');
        $this->campaign = $campaign;
        $this->booking = $booking;
        $this->position = $position;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaigns = $this->campaign->with('brand')->latest()->paginate(10);
        foreach ($campaigns->items() as $item) {
            $posIdList = json_decode($item->position_list, true);
            $posList = [];
            foreach ($posIdList as $posId) {
                $postion =  $this->position->find($posId);
                $posList[] = [
                    'id' => $postion->id,
                    'name'=>$postion->name . ', ' . $postion->store->name
                ];
            }
            $item->position_list = $posList;
            $item->from_ts = date('m/d/Y', $item->from_ts);
            $item->to_ts = date('m/d/Y', $item->to_ts);
        }
        return $this->sendResponse($campaigns, 'Campaign list');
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
     * @param CampaignRequest $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function store(CampaignRequest $request)
    {
        try{
            $positionList = $request->get('position_list');

            $campaignArr = [
                'name' => $request->get('name'),
                'contract_code' =>  $request->get('contract_code'),
                'license_code' => $request->get('license_code'),
                'brand_id' => $request->get('brand_id') ,
                'days_diff' => $request->get('days_diff'),
                'position_price' => $request->get('position_price'),
                'position_list' => json_encode(array_column($positionList, 'id')),
                'from_ts' => $request->get('from_ts'),
                'to_ts' => $request->get('to_ts'),
                'discount_type' => $request->get('discount_type'),
                'discount_value' => $request->get('discount_value'),
                'discount_max' => $request->get('discount_max'),
                'total_discount' => $request->get('total_discount'),
                'total_price' => $request->get('total_price'),
                'created_by' => auth()->user()->id,
            ];
            $campaign = $this->campaign->create($campaignArr);

            foreach($positionList as $pos) {
                $booking = [
                    'campaign_id' => $campaign->id,
                    'position_id' => $pos['id'],
                    'buffer_ts' => $pos['buffer_days'] * 24 * 3600,
                    'from_ts' => $request->get('from_ts'),
                    'to_ts' => $request->get('to_ts'),
                ];
                $newBooking = $this->booking->create($booking);
            }

            return $this->sendResponse($campaign, 'Campaign Created Successfully');
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
    public function update(CampaignRequest $request, $id)
    {
        $campaign = $this->campaign->findOrFail($id);
        $data = [
            'name' => $request->get('name'),
            'description'=> $request->get('description'),
            'status' =>  $request->get('status'),
            'image_url'=> $request->get('image_url'),
            'store_id' => $request->get('store'),
            'channel'=> $request->get('channel'),
            'buffer_days' =>  $request->get('buffer_days'),
            'unit'=> $request->get('unit'),
            'price'=> $request->get('price'),
        ];

        $campaign->update($data);

        return $this->sendResponse($campaign, 'Campaign Information has been updated');
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
        $campaign = $this->campaign->findOrFail($id);
        $campaign->delete();

        return $this->sendResponse($campaign, 'Campaign has been Deleted');
    }
}
