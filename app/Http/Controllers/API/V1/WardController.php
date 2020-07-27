<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Ward;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WardController extends BaseController
{
    /**
     * @var Ward
     */
    protected $ward = null;

    /***
     * WardController constructor.
     * @param Ward $ward
     */
    public function __construct(Ward $ward)
    {
        $this->middleware('auth:api');
        $this->ward = $ward;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $wards = $this->ward->all();
        foreach ($wards as $ward) {
            $data[] = ['id' => $ward->id, 'name' => $ward->name];
        }

        return $this->sendResponse(['data' => $data], 'District list');
    }

    public function list(Request $request) {
        $dId = $request->input('district_id') ?? null;
        if($dId) {
            $wards = $this->ward->where('district_id', $dId)->get();
            $data = [];
            foreach ($wards as $ward) {
                $data[] = ['id' => $ward->id, 'name' => $ward->name];
            }

            return $this->sendResponse(['data' => $data], 'Ward list');
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
     * Ward a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function store(WardRequest $request)
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
//            $ward = $this->ward->create([
//                'name' => $request->get('name'),
////                'status' => Ward::ACTIVE,
//                'image_url' => $imageUrl,
//                'buffer_days' => $request->get('buffer_days')
//            ]);
//            return $this->sendResponse($ward, 'Ward Created Successfully');
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
//    public function update(WardRequest $request, $id)
//    {
//        $ward = $this->ward->findOrFail($id);
//
//        $ward->update($request->all());
//
//        return $this->sendResponse($ward, 'Ward Information has been updated');
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
//        $ward = $this->ward->findOrFail($id);
//        $ward->delete();
//
//        return $this->sendResponse($ward, 'Ward has been Deleted');
//    }
}
