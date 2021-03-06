<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Campaigns\CampaignRequest;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\Position;
use App\Models\Booking;


use Exception;
use Illuminate\Support\Facades\Auth;
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
        // todo use user_id in param instead /campaign?user_id=xxx
        $user = Auth::user();
        $brands = $user->brands()->get();
        $brandIds = [];

        foreach ($brands as $brand) {
            array_push($brandIds, $brand->id);
        }

        // todo check pagination
        if(count($brandIds) > 0) {
            $campaigns = $this->campaign->whereIn('brand_id', $brandIds)->with('brand')->latest()->paginate(20);
        } else {
            $campaigns = $this->campaign->with('brand')->latest()->paginate(20);
        }

        foreach ($campaigns->items() as $item) {
            $posIdList = json_decode($item->position_list, true);
            $posList = [];
            foreach ($posIdList as $posId) {
                $position =  $this->position->find($posId);
                array_push($posList, $position);
            }
            $item->position_list = $posList;
            $item->from_ts = date('m/d/Y', $item->from_ts);
            $item->to_ts = date('m/d/Y', $item->to_ts);

            $company = Company::find($item->brand->company_id);
            $item->company = $company;
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
                'brand_id' => $request->get('brand')['id'] ,
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
                'status' => $request->has('status') ? $request->get('status')['value'] : $this->campaign->getDefaultStatus(),
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

        $positionList = $request->get('position_list');

        $data = [
            'name' => $request->get('name'),
            'contract_code' =>  $request->get('contract_code'),
            'license_code' => $request->get('license_code'),
            'brand_id' => $request->get('brand')['id'] ,
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
            'status' => $request->has('status') ? $request->get('status')['value'] : $campaign->status,
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
        $bookings = $this->booking->where('campaign_id', $id)->get();
        foreach ($bookings as $b) {
            $b->delete();
        }
        $campaign = $this->campaign->findOrFail($id);
        $campaign->delete();

        return $this->sendResponse($campaign, 'Campaign has been Deleted');
    }
}
