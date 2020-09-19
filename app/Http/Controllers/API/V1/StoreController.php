<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Stores\StoreRequest;
use App\Models\Booking;
use App\Models\Position;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class StoreController extends BaseController
{
    /**
     * @var Store
     */
    protected $store = null;

    /***
     * StoreController constructor.
     * @param Store $store
     */
    public function __construct(Store $store)
    {
        $this->middleware('auth:api')->except(['search']);
        $this->store = $store;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = $this->store->latest()->paginate(10);

        return $this->sendResponse($stores,'Store list');
    }

    public function list(Request $request)
    {
        $provinceId = $request->input('province_id');
        $districtId = $request->input('district_id');
        $wardId = $request->input('ward_id');
        $level = $request->input('store_level');

        $condition = true;

        if($provinceId) {
            $condition .= sprintf(" AND province_id = %s", $provinceId);
        }

        if($districtId) {
            $condition .= sprintf(" AND district_id = %s", $districtId);
        }

        if($wardId) {
            $condition .= sprintf(" AND ward_id = %s", $wardId);
        }

        if($level) {
            $condition .= sprintf(" AND level ='%s'", $level);
        }

        $query = "select * from stores where " . $condition;

        $stores = DB::select($query);

        $data = [];
        foreach ($stores as $store) {
            $data[] = [
                'id' => $store->id,
                'name' => $store->name,
                'code' => $store->code,
                'level' => $store->level,
                'ward' => $store->ward,
                'district' => $store->district,
                'province' => $store->province,
            ];
        }

        return $this->sendResponse(['data' => $data], 'Store list');
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $store = $this->store->create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'status' => $request->get('status'),
        ]);

        return $this->sendResponse($store, 'Store Created Successfully');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(StoreRequest $request, $id)
    {
        $store = $this->store->findOrFail($id);

        $store->update($request->all());

        // update pivot table
//        $tag_ids = [];
//        foreach ($request->get('tags') as $tag) {
//            $tag_ids[] = $tag['id'];
//        }
//        $store->tags()->sync($tag_ids);

        return $this->sendResponse($store, 'Store Information has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $store = $this->store->findOrFail($id);
        $store->delete();

        return $this->sendResponse($store, 'Store has been Deleted');
    }

    /**
     * Search store by latitude, longitude
     * @param Request $request
     */
    public function search(Request $request) {
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');

        $radius = $request->get('radius') ?? 1.0; // in km

        $query = sprintf('SELECT
            id, name, latitude , longitude ,
            (
                6371 * acos(
                    cos(radians(%f)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(%f)) +
                    sin(radians(%f)) * sin(radians(latitude))
                )
            ) AS distance
        FROM
            stores s
        HAVING
            distance < %f
        ORDER BY
            distance', $latitude, $longitude, $latitude, $radius);

        $stores = DB::select($query);

        $rs = [];
        foreach ($stores as $store) {
            $storeInfo = [
                'store_id'=> $store->id,
                'store_name'=> $store->name,
                'latitude'=> floatval($store->latitude),
                'longitude'=> floatval($store->longitude),
                'positions' => [],
            ];
            $positions = Position::where('store_id', $store->id)->get();
            foreach($positions as $pos) {
                $posArr = $pos->toArray();
                $bookings = Booking::where('position_id', $pos->id)->get();
                foreach ($bookings as $b) {
                        $posArr['bookings'][] = $b->toArray();
                }
                $storeInfo['positions'][] = $posArr;
            }
            $rs[] = $storeInfo;
        }

        return response()->json($rs, 200);
    }
}
