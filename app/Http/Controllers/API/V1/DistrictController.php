<?php

namespace App\Http\Controllers\API\V1;

use App\Models\District;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DistrictController extends BaseController
{
    /**
     * @var District
     */
    protected $district = null;

    /***
     * DistrictController constructor.
     * @param District $district
     */
    public function __construct(District $district)
    {
        $this->middleware('auth:api');
        $this->district = $district;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $districts = $this->district->all();
        foreach ($districts as $district) {
            $data[] = ['id' => $district->id, 'name' => $district->name];
        }

        return $this->sendResponse(['data' => $data], 'District list');
    }


    public function list(Request $request) {
        $provinceId = $request->input('province_id') ?? null;
        if($provinceId) {
            $districts = $this->district->where('province_id', $provinceId)->get();
            $data = [];
            foreach ($districts as $district) {
                $data[] = ['id' => $district->id, 'name' => $district->name];
            }

            return $this->sendResponse(['data' => $data], 'District list');
        }

        return $this->index();

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
     * District a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function store(DistrictRequest $request)
//    {
//        $imageUrl = $request->get('image_url');
//        if ($imageUrl) {
//            try {
//                Storage::move($imageUrl, storage_path('public/'.$imageUrl));
//            } catch (Exception $exception) {
//                Log::error($exception);
//            }
//        }
//
//        try {
//            $district = $this->district->create([
//                'name' => $request->get('name'),
////                'status' => District::ACTIVE,
//                'image_url' => $imageUrl,
//                'buffer_days' => $request->get('buffer_days')
//            ]);
//            return $this->sendResponse($district, 'District Created Successfully');
//        } catch (Exception $e) {
//            Log::error($e);
//            return $this->sendError($e->getMessage(), [], 400);
//        }
//    }

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
//    public function update(DistrictRequest $request, $id)
//    {
//        $district = $this->district->findOrFail($id);
//
//        $district->update($request->all());
//
//        return $this->sendResponse($district, 'District Information has been updated');
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//        $this->authorize('isAdmin');
//        $district = $this->district->findOrFail($id);
//        $district->delete();
//
//        return $this->sendResponse($district, 'District has been Deleted');
//    }
}
