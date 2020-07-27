<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Province;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProvinceController extends BaseController
{
    /**
     * @var Province
     */
    protected $province = null;

    /***
     * ProvinceController constructor.
     * @param Province $province
     */
    public function __construct(Province $province)
    {
        $this->middleware('auth:api');
        $this->province = $province;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $provinces = $this->province->all();
        foreach ($provinces as $province) {
            $data[] = ['id' => $province->id, 'name' => $province->name];
        }

        return $this->sendResponse(['data' => $data], 'Province list');
    }

    public function list(Request $request) {
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
     * Province a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function store(ProvinceRequest $request)
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
//            $province = $this->province->create([
//                'name' => $request->get('name'),
////                'status' => Province::ACTIVE,
//                'image_url' => $imageUrl,
//                'buffer_days' => $request->get('buffer_days')
//            ]);
//            return $this->sendResponse($province, 'Province Created Successfully');
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
//    public function update(ProvinceRequest $request, $id)
//    {
//        $province = $this->province->findOrFail($id);
//
//        $province->update($request->all());
//
//        return $this->sendResponse($province, 'Province Information has been updated');
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
//        $province = $this->province->findOrFail($id);
//        $province->delete();
//
//        return $this->sendResponse($province, 'Province has been Deleted');
//    }
}
